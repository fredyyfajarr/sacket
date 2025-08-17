<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class FixOrderItems extends Command
{
    protected $signature = 'orders:fix-items {--order-id= : Specific order ID to fix}';
    protected $description = 'Fix orders that are paid but missing order items';

    public function handle()
    {
        $orderId = $this->option('order-id');

        if ($orderId) {
            $orders = Order::where('id', $orderId)->get();
        } else {
            // Ambil semua order yang paid tapi tidak punya items
            $orders = Order::where('status', 'paid')
                          ->doesntHave('items')
                          ->with('event.ticketCategories')
                          ->get();
        }

        if ($orders->isEmpty()) {
            $this->info('No orders need fixing.');
            return;
        }

        $this->info("Found {$orders->count()} orders to fix.");

        foreach ($orders as $order) {
            $this->info("Processing Order #{$order->id} - {$order->order_number}");

            // Ambil ticket category pertama dari event
            $ticketCategory = $order->event->ticketCategories()->first();

            if (!$ticketCategory) {
                $this->error("  No ticket categories found for event: {$order->event->name}");
                continue;
            }

            // Hitung quantity berdasarkan total price
            $quantity = intval($order->total_price / $ticketCategory->price);

            if ($quantity <= 0) {
                $this->error("  Invalid quantity calculation for order {$order->id}");
                continue;
            }

            // Cek stock
            if ($ticketCategory->stock < $quantity) {
                $this->warn("  Insufficient stock. Required: {$quantity}, Available: {$ticketCategory->stock}");
                // Tetap lanjut tapi dengan quantity yang tersedia
                $quantity = $ticketCategory->stock;
            }

            // Buat order items
            for ($i = 0; $i < $quantity; $i++) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_category_id' => $ticketCategory->id,
                    'price' => $ticketCategory->price,
                    'quantity' => 1,
                    'unique_code' => Str::uuid(),
                ]);
            }

            // Kurangi stock
            $ticketCategory->decrement('stock', $quantity);

            $this->info("  ✓ Created {$quantity} ticket(s) for order {$order->order_number}");
        }

        $this->info('All orders have been processed.');
    }
}
