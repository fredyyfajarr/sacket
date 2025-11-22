<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event dengan fitur Filter & Live Search.
     */
    public function index(Request $request)
    {
        // 1. Logic Banner (Event Populer) - Tidak terpengaruh filter
        $popularEvents = Event::with('ticketCategories')
            ->where('start_date', '>=', now())
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(3)
            ->get();

        // 2. Query Dasar
        $query = Event::query()->with('ticketCategories')->where('start_date', '>=', now());

        // --- FILTER LOGIC ---

        // Filter: Search (Nama & Lokasi)
        $query->when($request->search, function ($q, $search) {
            return $q->where(function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        });

        // Filter: Lokasi Spesifik
        $query->when($request->location, fn($q, $loc) => $q->where('location', $loc));

        // Filter: Harga Maksimal (Budget)
        $query->when($request->price_max, function ($q, $price) {
            $q->whereHas('ticketCategories', function ($subQ) use ($price) {
                $subQ->where('price', '<=', $price);
            });
        });

        // Filter: Sorting
        if ($request->sort === 'cheapest') {
            $query->withMin('ticketCategories', 'price')
                  ->orderBy('ticket_categories_min_price', 'asc');
        } elseif ($request->sort === 'soonest') {
            $query->orderBy('start_date', 'asc');
        } else {
            $query->latest(); // Default: Terbaru diupload
        }

        // Eksekusi Pagination
        $events = $query->paginate(9)->withQueryString();

        // --- AJAX RESPONSE ---
        // Jika request datang dari Javascript (Live Search), kembalikan hanya potongannya
        if ($request->ajax()) {
            return view('events.partials.list', compact('events'))->render();
        }

        // Data untuk Dropdown Lokasi
        $locations = Event::where('start_date', '>=', now())
                        ->select('location')
                        ->distinct()
                        ->pluck('location');

        return view('events.index', compact('popularEvents', 'events', 'locations'));
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }
}
