<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplikat setiap kali seeding
        TicketCategory::query()->delete();
        Event::query()->delete();

        // Membuat 50 Event baru menggunakan Factory
        Event::factory(50)
            ->has(
                // Untuk setiap Event, buat antara 1 sampai 3 jenis kategori tiket
                TicketCategory::factory()->count(rand(1, 3))
            )
            ->create();
    }
}
