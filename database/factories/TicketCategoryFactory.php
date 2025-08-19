<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TicketCategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['Reguler', 'VIP', 'Presale 1', 'Early Bird', 'Festival'];
        return [
            'name' => $this->faker->randomElement($categories),
            'price' => $this->faker->numberBetween(15, 100) * 10000,
            'stock' => $this->faker->numberBetween(100, 1000),
            'sale_start_date' => now()->subDays(rand(1, 5)),
            'sale_end_date' => now()->addDays(rand(10, 30)),
        ];
    }
}
