<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Konser ' . $this->faker->words(3, true);
        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(3),
            'location' => $this->faker->city,
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'end_date' => $this->faker->dateTimeBetween('+3 months', '+6 months'),
            'image' => 'images/event-' . $this->faker->numberBetween(1, 11) . '.jpg', // Asumsi Anda punya 11 gambar
        ];
    }
}
