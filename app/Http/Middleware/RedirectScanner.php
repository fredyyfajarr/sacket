<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectScanner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika pengguna login DAN memiliki role 'scanner' DAN sedang mencoba akses halaman utama
        if (Auth::check() && Auth::user()->hasRole('scanner') && $request->is('/')) {
            // Jika semua kondisi terpenuhi, paksa redirect ke halaman scanner
            return redirect('/scanner');
        }

        return $next($request);
    }
}
