<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        // HENTIKAN SEMUANYA DAN TAMPILKAN ROLE PENGGUNA SAAT INI
        // dd($user->getRoleNames());
        $url = ''; // Siapkan variabel URL

        // Tambahkan kondisi untuk role 'scanner'
        if ($user->hasRole('admin')) {
            $url = '/admin';
        } elseif ($user->hasRole('scanner')) {
            $url = '/scanner';
        } else {
            $url = route('events.index');
        }

        return redirect($url);
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
