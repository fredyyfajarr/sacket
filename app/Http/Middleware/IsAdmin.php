<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah pengguna sudah login DAN memiliki role 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            // Jika ya, izinkan untuk melanjutkan.
            return $next($request);
        }

        // Jika tidak, kembalikan ke halaman utama dengan pesan error.
        return redirect('/')->withErrors('Anda tidak memiliki hak akses ke halaman ini.');
    }
}
