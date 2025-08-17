<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data event pertama (yang sudah ada)
        DB::table('events')->insert([
            [
                'name' => 'The Sound Project 2025',
                'slug' => 'the-sound-project-2025',
                'description' => 'A two-day music festival featuring local and international artists.',
                'location' => 'Allianz Ecopark Ancol',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(31),
                'image' => 'images/event-1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $event1_id = DB::table('events')->where('slug', 'the-sound-project-2025')->first()->id;

        DB::table('ticket_categories')->insert([
            [
                'event_id' => $event1_id,
                'name' => 'Early Bird',
                'price' => 150000.00,
                'stock' => 100,
                'sale_start_date' => now()->subDays(10),
                'sale_end_date' => now()->subDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => $event1_id,
                'name' => 'Presale 1',
                'price' => 200000.00,
                'stock' => 200,
                'sale_start_date' => now()->subDays(4),
                'sale_end_date' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => $event1_id,
                'name' => 'Normal',
                'price' => 250000.00,
                'stock' => 500,
                'sale_start_date' => now()->subDays(1),
                'sale_end_date' => now()->addDays(29),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Tambahan 10 event dummy
        $events = [
            [
                'name' => 'Jakarta Jazz Festival',
                'slug' => 'jakarta-jazz-festival',
                'description' => 'A night full of smooth jazz and incredible performances.',
                'location' => 'JIExpo Kemayoran',
                'start_date' => now()->addDays(45),
                'end_date' => now()->addDays(45),
                'image' => 'images/event-2.jpg',
                'ticket_categories' => [
                    ['name' => 'Daily Pass', 'price' => 300000.00, 'stock' => 500],
                    ['name' => 'VIP', 'price' => 750000.00, 'stock' => 50]
                ]
            ],
            [
                'name' => 'Rock Legends Concert',
                'slug' => 'rock-legends-concert',
                'description' => 'An electrifying concert with the greatest rock bands of all time.',
                'location' => 'Gelora Bung Karno',
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(60),
                'image' => 'images/event-3.jpg',
                'ticket_categories' => [
                    ['name' => 'Festival', 'price' => 450000.00, 'stock' => 1000],
                    ['name' => 'Tribune A', 'price' => 600000.00, 'stock' => 300]
                ]
            ],
            [
                'name' => 'Pop Star Extravaganza',
                'slug' => 'pop-star-extravaganza',
                'description' => 'The ultimate pop music party with your favorite idols.',
                'location' => 'ICE BSD',
                'start_date' => now()->addDays(75),
                'end_date' => now()->addDays(75),
                'image' => 'images/event-4.jpg',
                'ticket_categories' => [
                    ['name' => 'Standing', 'price' => 350000.00, 'stock' => 800],
                    ['name' => 'Seating', 'price' => 500000.00, 'stock' => 400]
                ]
            ],
            [
                'name' => 'Acoustic Night',
                'slug' => 'acoustic-night',
                'description' => 'An intimate evening of acoustic music and heartwarming melodies.',
                'location' => 'Balai Sidang Jakarta',
                'start_date' => now()->addDays(90),
                'end_date' => now()->addDays(90),
                'image' => 'images/event-5.jpg',
                'ticket_categories' => [
                    ['name' => 'General', 'price' => 100000.00, 'stock' => 200],
                ]
            ],
            [
                'name' => 'EDM Party Wave',
                'slug' => 'edm-party-wave',
                'description' => 'Get ready to dance all night to the best electronic beats.',
                'location' => 'Ancol Beach City',
                'start_date' => now()->addDays(100),
                'end_date' => now()->addDays(100),
                'image' => 'images/event-6.jpg',
                'ticket_categories' => [
                    ['name' => 'Presale', 'price' => 250000.00, 'stock' => 600],
                    ['name' => 'On The Spot', 'price' => 350000.00, 'stock' => 200]
                ]
            ],
            [
                'name' => 'Classic Orchestra',
                'slug' => 'classic-orchestra',
                'description' => 'A beautiful night of timeless classical music.',
                'location' => 'Teater Jakarta',
                'start_date' => now()->addDays(115),
                'end_date' => now()->addDays(115),
                'image' => 'images/event-7.jpg',
                'ticket_categories' => [
                    ['name' => 'Reguler', 'price' => 200000.00, 'stock' => 300],
                    ['name' => 'Gold', 'price' => 400000.00, 'stock' => 100]
                ]
            ],
            [
                'name' => 'Hip Hop Nation',
                'slug' => 'hip-hop-nation',
                'description' => 'The biggest names in hip hop and rap together on one stage.',
                'location' => 'Istora Senayan',
                'start_date' => now()->addDays(130),
                'end_date' => now()->addDays(130),
                'image' => 'images/event-8.jpg',
                'ticket_categories' => [
                    ['name' => 'General Admission', 'price' => 300000.00, 'stock' => 700],
                    ['name' => 'Backstage Pass', 'price' => 1000000.00, 'stock' => 20]
                ]
            ],
            [
                'name' => 'Indie Music Fest',
                'slug' => 'indie-music-fest',
                'description' => 'Discover new sounds and emerging artists from the indie scene.',
                'location' => 'Lap. Banteng',
                'start_date' => now()->addDays(145),
                'end_date' => now()->addDays(146),
                'image' => 'images/event-9.jpg',
                'ticket_categories' => [
                    ['name' => '2-Day Pass', 'price' => 250000.00, 'stock' => 500]
                ]
            ],
            [
                'name' => 'R&B Soulful Vibes',
                'slug' => 'rnb-soulful-vibes',
                'description' => 'An evening dedicated to soulful R&B music.',
                'location' => 'The Pallas SCBD',
                'start_date' => now()->addDays(160),
                'end_date' => now()->addDays(160),
                'image' => 'images/event-10.jpg',
                'ticket_categories' => [
                    ['name' => 'Standing', 'price' => 220000.00, 'stock' => 400],
                ]
            ],
            [
                'name' => 'K-Pop Fan Meeting',
                'slug' => 'k-pop-fan-meeting',
                'description' => 'Meet and greet with a famous K-Pop idol.',
                'location' => 'Tennis Indoor Senayan',
                'start_date' => now()->addDays(175),
                'end_date' => now()->addDays(175),
                'image' => 'images/event-11.jpg',
                'ticket_categories' => [
                    ['name' => 'Reguler', 'price' => 500000.00, 'stock' => 800],
                    ['name' => 'Fan Sign', 'price' => 2000000.00, 'stock' => 100]
                ]
            ]
        ];

        foreach ($events as $eventData) {
            $ticketCategories = $eventData['ticket_categories'];
            unset($eventData['ticket_categories']);

            DB::table('events')->insert($eventData);
            $event_id = DB::table('events')->where('slug', $eventData['slug'])->first()->id;

            foreach ($ticketCategories as $ticketCategory) {
                DB::table('ticket_categories')->insert([
                    'event_id' => $event_id,
                    'name' => $ticketCategory['name'],
                    'price' => $ticketCategory['price'],
                    'stock' => $ticketCategory['stock'],
                    'sale_start_date' => now()->subDays(rand(1, 10)),
                    'sale_end_date' => now()->addDays(rand(10, 20)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
