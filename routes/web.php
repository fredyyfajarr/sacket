<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketScannerController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\Admin\EventController as AdminEventController;


Route::get('/admin/login', fn()=> redirect('/login'))->name('filament.admin.auth.login');
/*
|--------------------------------------------------------------------------
| Rute Publik (Bisa diakses siapa saja)
|--------------------------------------------------------------------------
*/
Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

/*
|--------------------------------------------------------------------------
| Rute Autentikasi (Untuk semua pengguna yang sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Rute "My Tickets"
    Route::get('/my-tickets', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-tickets/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/my-tickets/{orderItem}/download', [OrderController::class, 'downloadTicket'])->name('orders.download');

    // Rute Proses Pembayaran
    Route::post('/events/{event}/pay', [OrderController::class, 'initiatePayment'])->name('orders.pay');
    Route::get('/payment/success', [OrderController::class, 'success'])->name('orders.success');

    // Rute Validasi Kode Promo
    Route::post('/promo-code/validate', [PromoCodeController::class, 'validateCode'])->name('promo.validate');
});


/*
|--------------------------------------------------------------------------
| Rute Khusus Admin (Hanya untuk role 'admin')
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin']) // Hanya 'admin' yang boleh masuk ke sini
    ->group(function () {
        // Redirect dashboard ke halaman utama panel admin
        Route::get('/dashboard', fn () => redirect('/admin'))->name('dashboard');

        // Catatan: Rute /admin/scanner dipindahkan keluar dari grup ini
    });


/*
|--------------------------------------------------------------------------
| Rute Khusus Scanner (Untuk role 'admin' DAN 'scanner')
|--------------------------------------------------------------------------
*/
// Rute ini sengaja diletakkan di luar grup /admin agar bisa diakses dengan URL yang lebih sederhana
Route::get('/scanner', [TicketScannerController::class, 'index'])
    ->middleware(['auth', 'role:admin|scanner']) // Bisa diakses admin ATAU scanner
    ->name('scanner.index');

Route::post('/ticket/verify', [TicketScannerController::class, 'verify'])
    ->middleware(['auth', 'role:admin|scanner']) // Bisa diakses admin ATAU scanner
    ->name('ticket.verify');


/*
|--------------------------------------------------------------------------
| File Rute Bawaan
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/profile.php'; // Biasanya sudah di-handle oleh auth.php atau layout
