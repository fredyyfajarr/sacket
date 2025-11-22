<?php

namespace App\Http\Controllers;

use App\Models\Event; // Pastikan import model Event
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class TicketScannerController extends Controller
{
    /**
     * Menampilkan halaman scanner tiket.
     */
    public function index(): View
    {
        // Ambil event yang belum selesai (hari ini atau masa depan) untuk dipilih Admin
        $events = Event::where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get();

        return view('admin.scanner.index', compact('events'));
    }

    /**
     * Memverifikasi kode unik tiket via API.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'unique_code' => 'required|string',
            'event_id'    => 'required|exists:events,id', // Validasi Event ID dari dropdown scanner
        ]);

        $ticket = OrderItem::where('unique_code', $request->unique_code)
            ->with(['order.event', 'ticketCategory'])
            ->first();

        // Kasus 1: Tiket tidak ditemukan di database manapun
        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket Tidak Dikenali!',
            ], 404);
        }

        // Kasus 1.5: Tiket valid, tapi BUKAN untuk event ini
        // (Logika "Benar tidaknya tiket untuk konser ini")
        if ($ticket->order->event_id != $request->event_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket Salah Event!',
                'detail' => 'Tiket ini untuk event: ' . $ticket->order->event->name
            ], 400);
        }

        // Kasus 2: Pesanan belum lunas
        if ($ticket->order->status !== 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket Belum Lunas!',
                'data' => $ticket
            ], 422);
        }

        // Kasus 3: Tiket sudah pernah di-scan
        if ($ticket->checked_in_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket Sudah Digunakan!',
                'checked_in_at' => $ticket->checked_in_at->format('d M Y, H:i:s'),
                'data' => $ticket
            ], 409);
        }

        // Kasus 4: Tiket valid dan berhasil check-in
        DB::transaction(function () use ($ticket) {
            $ticket->checked_in_at = now();
            $ticket->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in Berhasil!',
            'checked_in_at' => $ticket->checked_in_at->format('H:i:s'),
            'data' => $ticket
        ]);
    }
}
