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
        // PRODUCTION: Keep basic webhook logging for monitoring
        Log::info('Midtrans webhook received', [
            'order_id' => $request->input('order_id'),
            'transaction_status' => $request->input('transaction_status'),
            'ip' => $request->ip()
        ]);

        // 1) Konfigurasi Midtrans
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$serverKey    = (string) config('midtrans.server_key');

        // 2) Ambil payload
        $payload = $request->all();

        // 3) Basic validation
        $requiredFields = ['order_id','transaction_status','status_code','gross_amount','signature_key'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                Log::error('Webhook missing field: ' . $field, ['order_id' => $payload['order_id'] ?? 'unknown']);
                return response()->json(['message' => 'Bad Request'], 400);
            }
        }

        $orderId          = (string) $payload['order_id'];
        $transactionStatus= (string) $payload['transaction_status'];
        $statusCode       = (string) $payload['status_code'];
        $grossAmountStr   = (string) $payload['gross_amount'];
        $signatureKey     = (string) $payload['signature_key'];
        $paymentType      = (string) ($payload['payment_type'] ?? '');
        $fraudStatus      = (string) ($payload['fraud_status'] ?? '');

        // 4) Signature validation
        $expected = hash('sha512', $orderId.$statusCode.$grossAmountStr.Config::$serverKey);

        if (Config::$isProduction && !hash_equals($expected, $signatureKey)) {
            Log::error('Invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 5) Status mapping
        $newStatus = match ($transactionStatus) {
            'settlement' => 'paid',
            'capture' => ($paymentType === 'credit_card' && $fraudStatus === 'challenge') ? 'pending' : 'paid',
            'pending' => 'pending',
            'cancel', 'expire', 'deny', 'failure' => 'canceled',
            default => 'pending',
        };

        try {
            return DB::transaction(function () use ($orderId, $newStatus, $payload, $grossAmountStr) {

                $order = Order::where('order_number', $orderId)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    // Auto-create order if needed
                    $userId  = (int) ($payload['custom_field1'] ?? 0);
                    $eventId = (int) ($payload['custom_field2'] ?? 0);
                    $cust    = $payload['customer_details'] ?? ['first_name' => '', 'email' => ''];

                    if (!$userId || !$eventId) {
                        Log::error('Cannot auto-create order', ['order_id' => $orderId]);
                        return response()->json(['message' => 'Order not found'], 404);
                    }

                    $order = Order::create([
                        'user_id'       => $userId,
                        'event_id'      => $eventId,
                        'order_number'  => $orderId,
                        'customer_name' => $cust['first_name'] ?? '',
                        'customer_email'=> $cust['email'] ?? '',
                        'total_price'   => (int) round((float) $grossAmountStr),
                        'status'        => 'pending',
                    ]);

                    Log::info('Order auto-created', ['order_id' => $order->id]);
                }

                // Update status
                if ($order->status !== $newStatus) {
                    $order->update(['status' => $newStatus]);
                    Log::info('Order status updated', [
                        'order_id' => $order->id,
                        'status' => $newStatus
                    ]);
                }

                // Create items for paid orders
                if ($newStatus === 'paid' && $order->items()->count() === 0) {
                    $this->createOrderItems($order, $payload);
                    $order->load('items.ticketCategory', 'event');

                    // Kirim email
                    // try {
                        Mail::to($order->customer_email)->send(new TicketPurchased($order));
                        Log::info('Ticket email successfully sent.', ['order_id' => $orderId]);
                    // } catch (\Exception $e) {
                    //     Log::error('Failed to send ticket email.', [
                    //         'order_id' => $orderId,
                    //         'error' => $e->getMessage()
                    //     ]);
                    // }
                }

                return response()->json([
                    'message' => 'OK',
                    'order_id' => $orderId,
                    'status' => $newStatus
                ], 200);
            });

        } catch (\Throwable $e) {
            Log::error('Webhook processing failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Error'], 500);
        }
    }

    private function createOrderItems(Order $order, array $payload): void
    {
        // Parse ticket info from custom_field3
        $tcId = null;
        $qty = null;
        $unitPrice = null;

        if (!empty($payload['custom_field3'])) {
            $parsed = json_decode((string) $payload['custom_field3'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                $tcId      = (int) ($parsed['ticket_category_id'] ?? 0);
                $qty       = (int) ($parsed['quantity'] ?? 0);
                $unitPrice = (int) ($parsed['unit_price'] ?? 0);
            }
        }

        // Fallback: parse from item_details
        if ((!$tcId || !$qty) && !empty($payload['item_details']) && is_array($payload['item_details'])) {
            $first = $payload['item_details'][0] ?? null;
            if ($first && isset($first['id'],$first['quantity'],$first['price'])) {
                $tcId      = (int) $first['id'];
                $qty       = (int) $first['quantity'];
                $unitPrice = (int) $first['price'];
            }
        }

        // Last fallback: use first category
        if (!$tcId || !$qty) {
            $ticketCategory = $order->event?->ticketCategories()->first();
            if ($ticketCategory) {
                $tcId = $ticketCategory->id;
                $calcQty = (int) floor(((int) $order->total_price) / (int) $ticketCategory->price);
                $qty = max($calcQty, 1);
                $unitPrice = (int) $ticketCategory->price;

                Log::warning('Using fallback ticket category', [
                    'order_id' => $order->id,
                    'tc_id' => $tcId,
                    'qty' => $qty
                ]);
            }
        }

        if (!$tcId || !$qty) {
            Log::error('Cannot determine ticket info for items', ['order_id' => $order->id]);
            return;
        }

        $ticketCategory = TicketCategory::find($tcId);
        if (!$ticketCategory) {
            Log::error('Ticket category not found', ['tc_id' => $tcId]);
            return;
        }

        // Adjust quantity if insufficient stock
        if ($ticketCategory->stock < $qty) {
            $qty = max($ticketCategory->stock, 0);
        }

        if ($qty <= 0) {
            Log::error('No stock available', ['order_id' => $order->id]);
            return;
        }

        try {
            // Decrement stock
            $ticketCategory->decrement('stock', $qty);

            // Create individual items
            for ($i = 0; $i < $qty; $i++) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'ticket_category_id' => $ticketCategory->id,
                    'price'              => $unitPrice ?? $ticketCategory->price,
                    'quantity'           => 1,
                    'unique_code'        => strtoupper(Str::random(8)) . '-' . $order->id . '-' . ($i + 1),
                ]);
            }

            Log::info('Order items created', [
                'order_id' => $order->id,
                'quantity' => $qty
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create order items', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            // Rollback stock
            $ticketCategory->increment('stock', $qty);
            throw $e;
        }
    }
}
