<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan seeder User dan Event
        $this->call([
            UserSeeder::class,
            EventSeeder::class,
        ]);

        // Sekarang, gunakan factory untuk membuat pesanan dummy
        // Ini akan membuat 20 pesanan yang terkait dengan user dan event yang sudah ada.
        Order::factory()->count(20)->create();
    }
}
