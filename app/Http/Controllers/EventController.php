<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // 1. Logic Popular Events
        // HANYA ambil yang published
        $popularEvents = Event::published()
            ->with('ticketCategories')
            ->where('start_date', '>=', now())
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(3)
            ->get();

        // 2. Query Dasar untuk Search & List
        // HANYA ambil yang published
        $query = Event::published()
            ->with('ticketCategories')
            ->where('start_date', '>=', now());

        // --- FILTER LOGIC (Sama seperti sebelumnya) ---

        // Filter: Search
        $query->when($request->search, function ($q, $search) {
            return $q->where(function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        });

        // Filter: Lokasi
        $query->when($request->location, fn($q, $loc) => $q->where('location', $loc));

        // Filter: Harga
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
            $query->latest();
        }

        // Eksekusi Pagination
        $events = $query->paginate(9)->withQueryString();

        // --- AJAX RESPONSE (Live Search) ---
        if ($request->ajax()) {
            return view('events.partials.list', compact('events'))->render();
        }

        // Data Dropdown Lokasi (Hanya dari event published)
        $locations = Event::published()
                        ->where('start_date', '>=', now())
                        ->select('location')
                        ->distinct()
                        ->pluck('location');

        return view('events.index', compact('popularEvents', 'events', 'locations'));
    }

    public function show(Event $event)
    {
        // Cek jika event draft diakses langsung via URL
        // Jika belum publish, tampilkan 404 (Not Found)
        if (!$event->is_published) {
            abort(404);
        }

        return view('events.show', compact('event'));
    }
}
