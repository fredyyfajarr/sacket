<x-guest-layout>
    @section('title', 'Login')

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md overflow-hidden">
        <h2 class="text-xl font-semibold text-gray-700 text-center mb-4">
            Masuk ke Akun Anda
        </h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Input Email --}}
            <div>
                <x-input-label for="email" value="Email" class="sr-only" />
                <x-text-input id="email"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="email" name="email" :value="old('email')" required autofocus placeholder="Email"
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            {{-- Input Password --}}
            <div class="mt-4">
                <x-input-label for="password" value="Password" class="sr-only" />
                <x-text-input id="password"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="password" name="password" required placeholder="Password" autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            </div>

            {{-- BAGIAN REMEMBER ME & FORGOT PASSWORD SUDAH DIHAPUS DI SINI --}}

            {{-- Tombol Masuk --}}
            <div class="mt-6">
                <x-primary-button
                    class="w-full py-3 text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 rounded-md shadow-sm">
                    Masuk
                </x-primary-button>
            </div>

            {{-- Link Daftar --}}
            <div class="mt-4 text-center text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-blue-500 hover:underline">
                    Daftar di sini
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
