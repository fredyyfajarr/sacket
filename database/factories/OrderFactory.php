<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Event;
use App\Models\User;
use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Ambil user dan event secara acak
        $user = User::inRandomOrder()->first();
        $event = Event::inRandomOrder()->first();
        $ticketCategory = $event ? $event->ticketCategories()->inRandomOrder()->first() : null;

        // Pastikan ada user, event, dan ticket category
        if (!$user || !$event || !$ticketCategory) {
            return [];
        }

        $quantity = $this->faker->numberBetween(1, 4);
        $totalPrice = $ticketCategory->price * $quantity;

        return [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'total_price' => $totalPrice,
            'status' => $this->faker->randomElement(['pending', 'paid']),
        ];
    }
}
