<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View; // Pastikan ini ada
use Illuminate\Http\JsonResponse;    // Pastikan ini ada

class TicketScannerController extends Controller
{
    /**
     * Menampilkan halaman scanner tiket.
     */
    public function index(): View
    {
        return view('admin.scanner.index');
    }

    /**
     * Memverifikasi kode unik tiket via API.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate(['unique_code' => 'required|string']);

        $ticket = OrderItem::where('unique_code', $request->unique_code)
            ->with(['order.event', 'ticketCategory'])
            ->first();

        // Kasus 1: Tiket tidak ditemukan
        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket Tidak Ditemukan!',
            ], 404);
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
            ], 409); // 409 Conflict
        }

        // Kasus 4: Tiket valid dan berhasil check-in
        DB::transaction(function () use ($ticket) {
            $ticket->checked_in_at = now();
            $ticket->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in Berhasil!',
            'checked_in_at' => $ticket->checked_in_at->format('d M Y, H:i:s'),
            'data' => $ticket
        ]);
    }
}
