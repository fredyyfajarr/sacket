<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\TicketScannerController;

// Midtrans webhook routes
Route::get('/midtrans-webhook/ping', fn () => response()->json([
    'ok' => true,
    'timestamp' => now()->toISOString()
]))->name('midtrans.ping');

Route::post('/midtrans-webhook', [MidtransController::class, 'handle'])
    ->name('midtrans.webhook');

    // RUTE BARU UNTUK API VERIFIKASI TIKET
// Route::post('/ticket/verify', [TicketScannerController::class, 'verify'])
//     // ->middleware('auth:sanctum') // Melindungi API
//     ->name('api.ticket.verify');
