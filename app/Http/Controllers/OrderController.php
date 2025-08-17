<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PromoCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with(['event', 'items'])->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Authorization check
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized access to order');
        }

        $order->load(['items.ticketCategory', 'event']);
        return view('orders.show', compact('order'));
    }

    public function downloadTicket(OrderItem $orderItem)
    {
        if (Auth::id() !== $orderItem->order->user_id) {
            abort(403, 'Unauthorized access to ticket');
        }

        // Check if order is paid
        if ($orderItem->order->status !== 'paid') {
            return back()->withErrors('Tiket hanya bisa didownload setelah pembayaran berhasil.');
        }

        $pdf = Pdf::loadView('orders.pdf', compact('orderItem'));
        return $pdf->download('ticket-' . $orderItem->unique_code . '.pdf');
    }

        public function initiatePayment(Request $request, Event $event)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'quantity' => 'required|integer|min:1|max:10',
            'promo_code' => 'nullable|string',
        ]);

        $ticketCategory = $event->ticketCategories()->findOrFail($validated['ticket_category_id']);

        if ($ticketCategory->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => "Stok tidak mencukupi. Tersisa: {$ticketCategory->stock} tiket."]);
        }

        $subtotal = $ticketCategory->price * $validated['quantity'];
        $discount = 0;
        $promoCode = null;

        if (!empty($validated['promo_code'])) {
            $promoCode = PromoCode::where('code', $validated['promo_code'])->first();
            if ($promoCode) {
                $isExpired = $promoCode->expires_at && $promoCode->expires_at->isPast();
                $isMaxedOut = $promoCode->max_uses !== null && $promoCode->uses >= $promoCode->max_uses;
                if (!$isExpired && !$isMaxedOut) {
                    if ($promoCode->type === 'percentage') {
                        $discount = $subtotal * ($promoCode->value / 100);
                    } else {
                        $discount = $promoCode->value;
                    }
                    $discount = min($discount, $subtotal);
                } else {
                    $promoCode = null;
                }
            }
        }

        $finalPrice = $subtotal - $discount;

        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $orderId = 'ORD-' . strtoupper(Str::random(12)) . '-' . time();

        try {
            $redirectUrl = DB::transaction(function() use ($validated, $event, $orderId, $finalPrice, $subtotal, $discount, $promoCode, $ticketCategory) {

                $order = Order::create([
                    'user_id'        => Auth::id(),
                    'event_id'       => $event->id,
                    'order_number'   => $orderId,
                    'customer_name'  => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'total_price'    => $finalPrice,
                    'status'         => 'pending',
                    'promo_code_id'  => $promoCode ? $promoCode->id : null,
                ]);

                if ($promoCode) {
                    $promoCode->increment('uses');
                }

                $itemDetails = [
                    [
                        'id'       => $ticketCategory->id,
                        'price'    => $ticketCategory->price,
                        'quantity' => $validated['quantity'],
                        'name'     => $ticketCategory->name . ' - ' . $event->name,
                    ]
                ];

                if ($discount > 0) {
                    $itemDetails[] = [
                        'id'       => 'PROMO-' . $promoCode->code,
                        'price'    => -$discount,
                        'quantity' => 1,
                        'name'     => 'Discount (' . $promoCode->code . ')',
                    ];
                }

                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => $finalPrice,
                    ],
                    'customer_details' => [
                        'first_name' => $validated['customer_name'],
                        'email'      => $validated['customer_email'],
                    ],
                    'item_details' => $itemDetails,
                    'callbacks' => [
                        // ===============================================
                        // PERBAIKAN REDIRECT ADA DI SINI
                        // ===============================================
                        'finish' => route('orders.success', ['order_id' => $orderId]),
                    ],
                ];

                $snap = Snap::createTransaction($params);

                return $snap->redirect_url;
            });

            return redirect($redirectUrl);

        } catch (\Exception $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal membuat transaksi. Silakan coba lagi.');
        }
    }

    public function success(Request $request)
    {
        $orderNumber = $request->query('order_id');

        if (!$orderNumber) {
            return redirect()->route('events.index')
                ->withErrors('Order ID tidak ditemukan.');
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('events.index')
                ->withErrors('Order tidak ditemukan.');
        }

        // Authorization check
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized access to order');
        }

        // Load relationships for display
        $order->load(['event', 'items.ticketCategory']);

        // PRODUCTION: Remove detailed logging, keep only important ones
        if ($order->status === 'pending') {
            Log::warning('Order pending on success page', ['order_number' => $orderNumber]);
        }

        return view('orders.success', [
            'order' => $order,
            'order_id' => $orderNumber,
            'status' => $order->status,
            'warning' => $order->status === 'pending' ? 'Pembayaran sedang diproses, mohon tunggu konfirmasi.' : null
        ]);
    }
}
