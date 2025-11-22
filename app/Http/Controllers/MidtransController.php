<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketCategory;
use Illuminate\Support\Str;
use App\Mail\TicketPurchased;
use Illuminate\Support\Facades\Mail;

class MidtransController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans webhook received', [
            'order_id' => $request->input('order_id'),
            'status' => $request->input('transaction_status'),
        ]);

        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$serverKey    = (string) config('midtrans.server_key');

        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Validasi Signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);
        if ($signatureKey !== $expectedSignature) {
            Log::error('Invalid signature detected', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'];
        $paymentType = $payload['payment_type'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        // Tentukan status baru
        $newStatus = null;
        if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {
                $newStatus = ($fraudStatus == 'challenge') ? 'pending' : 'paid';
            }
        } elseif ($transactionStatus == 'settlement') {
            $newStatus = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $newStatus = 'pending';
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $newStatus = 'canceled';
        }

        if (!$newStatus) {
            return response()->json(['message' => 'Status not mapped'], 200);
        }

        try {
            // Gunakan transaction agar status update & create items atomik
            DB::transaction(function () use ($orderId, $newStatus, $payload) {
                // Lock record untuk mencegah race condition
                $order = Order::where('order_number', $orderId)->lockForUpdate()->first();

                if (!$order) {
                    Log::error('Order not found for webhook', ['order_id' => $orderId]);
                    return;
                }

                // Jika status sudah final (paid/canceled), abaikan update selanjutnya kecuali jika perlu
                if (in_array($order->status, ['paid', 'canceled'])) {
                    return;
                }

                // Update status order
                $order->update(['status' => $newStatus]);
                Log::info("Order {$orderId} status updated to {$newStatus}");

                // Jika status PAID, buat Order Items (tiket) & kurangi stok
                if ($newStatus === 'paid') {
                    // Cek apakah items sudah ada agar tidak duplikat
                    if ($order->items()->doesntExist()) {
                        $this->createOrderItems($order, $payload);
                    }

                    // [FIX] Kirim email dalam blok try-catch terpisah
                    // Jika email gagal, jangan rollback transaksi DB (status tetap paid)
                    try {
                        // Reload order untuk memastikan relasi terambil jika diperlukan di email
                        $order->load('items.ticketCategory', 'event');

                        Mail::to($order->customer_email)->send(new TicketPurchased($order));
                        Log::info('Email tiket terkirim.', ['email' => $order->customer_email]);
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email tiket.', [
                            'order_id' => $orderId,
                            'error' => $e->getMessage()
                        ]);
                        // Disini kita biarkan saja, user masih bisa download tiket dari dashboard
                    }
                }
            });

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans webhook error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    private function createOrderItems(Order $order, array $payload)
    {
        $ticketCategoryId = null;
        $quantity = 0;
        $unitPrice = 0;

        // Prioritas 1: Ambil dari custom_field3 (format JSON yang kita buat di OrderController)
        if (!empty($payload['custom_field3'])) {
            $data = json_decode($payload['custom_field3'], true);
            if (is_array($data)) {
                $ticketCategoryId = $data['ticket_category_id'] ?? null;
                $quantity = $data['quantity'] ?? 0;
                $unitPrice = $data['unit_price'] ?? 0;
            }
        }

        // Prioritas 2 (Fallback): Ambil dari item_details Midtrans
        if ((!$ticketCategoryId || !$quantity) && !empty($payload['item_details'])) {
            // Logika ini beresiko jika item_details kosong atau format beda, tapi sebagai cadangan ok
            foreach ($payload['item_details'] as $item) {
                // Lewati item diskon/promo
                if (str_starts_with($item['id'], 'PROMO-')) continue;

                $ticketCategoryId = (int) $item['id'];
                $quantity = (int) $item['quantity'];
                $unitPrice = (int) $item['price'];
                break; // Asumsi 1 jenis tiket per transaksi
            }
        }

        if (!$ticketCategoryId || !$quantity) {
            Log::error('Gagal mengekstrak info tiket dari payload', ['order_id' => $order->order_number]);
            return;
        }

        $ticketCategory = TicketCategory::find($ticketCategoryId);

        if (!$ticketCategory) {
            Log::error('Kategori tiket tidak ditemukan saat webhook', ['id' => $ticketCategoryId]);
            return;
        }

        // Kurangi Stok
        if ($ticketCategory->stock < $quantity) {
            Log::warning('Stok habis saat pembayaran dikonfirmasi (Oversold risk)', ['order_id' => $order->id]);
            // Tetap lanjutkan atau batalkan? Biasanya tetap lanjut jika sudah bayar,
            // tapi set stok jadi 0 atau minus untuk ditangani admin.
            $ticketCategory->decrement('stock', $quantity);
        } else {
            $ticketCategory->decrement('stock', $quantity);
        }

        // Buat item tiket individual
        $itemsData = [];
        for ($i = 0; $i < $quantity; $i++) {
            $itemsData[] = [
                'order_id' => $order->id,
                'ticket_category_id' => $ticketCategory->id,
                'quantity' => 1, // 1 row per 1 tiket fisik (untuk QR code unik)
                'price' => $unitPrice,
                'unique_code' => strtoupper(Str::random(10)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        OrderItem::insert($itemsData);
        Log::info("Berhasil membuat {$quantity} tiket untuk Order {$order->order_number}");
    }
}
