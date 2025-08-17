<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketScannerController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\Admin\EventController as AdminEventController;

// Public routes
Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Payment success route
Route::get('/payment/success', [OrderController::class, 'success'])->name('orders.success');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // My tickets routes
    Route::get('/my-tickets', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-tickets/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/my-tickets/{orderItem}/download', [OrderController::class, 'downloadTicket'])->name('orders.download');

    // Payment initiation
    Route::post('/events/{event}/pay', [OrderController::class, 'initiatePayment'])->name('orders.pay');
});

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';

// Admin routes - ADD PROPER ADMIN MIDDLEWARE HERE
Route::get('/admin/login', fn () => redirect('/login'))->name('filament.admin.auth.login');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return redirect('/admin');
        })->name('dashboard');

        Route::get('/scanner', [TicketScannerController::class, 'index'])->name('scanner.index');

        // TAMBAHKAN RUTE VERIFIKASI DI SINI
        Route::post('/ticket/verify', [TicketScannerController::class, 'verify'])->name('ticket.verify');
    });

// ... rute lainnya
Route::post('/promo-code/validate', [PromoCodeController::class, 'validateCode'])->name('promo.validate');
