<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View; // <-- TAMBAHKAN BARIS INI

class EventController extends Controller
{
    /**
     * Menampilkan daftar semua event yang akan datang.
     */
     public function index(Request $request): View // <-- 2. Tambahkan Request $request
    {
        // Ambil 3 event "terpopuler" (tidak terpengaruh oleh filter)
        $popularEvents = Event::with('ticketCategories')
            ->where('start_date', '>=', now())
            ->latest()
            ->take(3)
            ->get();

        // Siapkan query dasar untuk event lainnya
        $query = Event::query()->with('ticketCategories')->where('start_date', '>=', now());

        // 3. Tambahkan logika PENCARIAN
        $query->when($request->search, function ($q, $search) {
            return $q->where('name', 'like', "%{$search}%")
                     ->orWhere('location', 'like', "%{$search}%");
        });

        // 4. Tambahkan logika FILTER LOKASI
        $query->when($request->location, function ($q, $location) {
            return $q->where('location', $location);
        });

        // Eksekusi query dengan paginasi
        // withQueryString() penting agar filter tetap aktif saat pindah halaman
        $events = $query->latest()->paginate(9)->withQueryString();

        // Ambil semua lokasi unik untuk ditampilkan di dropdown filter
        $locations = Event::where('start_date', '>=', now())->select('location')->distinct()->pluck('location');

        // Kirim semua variabel yang dibutuhkan ke view
        return view('events.index', compact('popularEvents', 'events', 'locations'));
    }

    /**
     * Menampilkan detail spesifik dari sebuah event.
     * Route Model Binding akan otomatis mencari event berdasarkan slug.
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }
}
