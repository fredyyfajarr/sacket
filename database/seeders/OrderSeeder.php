<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama yang ada di database
        $user = User::first();
        // Ambil 3 event pertama yang ada di database
        $events = Event::take(3)->get();

        if ($user && $events->count() > 0) {
            foreach ($events as $event) {
                // Ambil kategori tiket pertama dari setiap event
                $ticketCategory = $event->ticketCategories()->first();

                if ($ticketCategory) {
                    $order = Order::create([
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                        'customer_name' => $user->name,
                        'customer_email' => $user->email,
                        'total_price' => $ticketCategory->price * 2, // Beli 2 tiket per event
                        'status' => 'paid', // Statusnya sudah lunas
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'ticket_category_id' => $ticketCategory->id,
                        'quantity' => 2,
                        'price' => $ticketCategory->price,
                        'unique_code' => Str::uuid(),
                    ]);
                }
            }
        }
    }
}
