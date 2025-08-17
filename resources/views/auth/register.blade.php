<x-guest-layout>
    @section('title', 'Register')

    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md overflow-hidden">
        <h2 class="text-xl font-semibold text-gray-700 text-center mb-4">
            Buat Akun Baru
        </h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name" value="Nama Lengkap" class="sr-only" />
                <x-text-input id="name"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="text" name="name" :value="old('name')" required autofocus placeholder="Nama Lengkap"
                    autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" value="Email" class="sr-only" />
                <x-text-input id="email"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="email" name="email" :value="old('email')" required placeholder="Email"
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="Password" class="sr-only" />
                <x-text-input id="password"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="password" name="password" required placeholder="Password" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Konfirmasi Password" class="sr-only" />
                <x-text-input id="password_confirmation"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="password" name="password_confirmation" required placeholder="Konfirmasi Password"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
            </div>

            <div class="mt-6">
                <x-primary-button
                    class="w-full py-3 text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 rounded-md shadow-sm">
                    Daftar
                </x-primary-button>
            </div>

            <div class="mt-4 text-center text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-blue-500 hover:underline">
                    Login di sini
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
