@props(['isMobile' => false])

<nav>
    <ul class="{{ $isMobile ? 'flex flex-col items-center space-y-4 py-4' : 'flex items-center space-x-6' }}">

        @if (Auth::user()?->role !== 'scanner')
            <li>
                <a href="{{ route('events.index') }}"
                    class="{{ $isMobile ? 'text-gray-800' : 'text-gray-500 hover:text-blue-500' }}">
                    Home
                </a>
            </li>
        @endif

        @guest
            <li>
                <a href="{{ route('login') }}"
                    class="{{ $isMobile ? 'text-gray-800' : 'text-gray-500 hover:text-blue-500' }}">Login</a>
            </li>
            <li>
                <a href="{{ route('register') }}"
                    class="block text-center bg-blue-600 text-white font-semibold px-5 py-2 rounded-full hover:bg-blue-700">Register</a>
            </li>
        @endguest

        @auth
            {{-- Tampilkan link khusus berdasarkan role --}}
            @if (Auth::user()->hasRole('admin'))
                <li>
                    <a href="/admin"
                        class="{{ $isMobile ? 'text-gray-800 font-bold' : 'text-red-500 hover:text-red-700 font-semibold' }}">
                        Panel Admin
                    </a>
                </li>
            @endif

            @if (Auth::user()->hasRole('user'))
                <li>
                    <a href="{{ route('orders.index') }}"
                        class="{{ $isMobile ? 'text-gray-800' : 'text-gray-500 hover:text-blue-500' }}">
                        My Tickets
                    </a>
                </li>
            @endif

            {{-- Dropdown Pengguna (tetap tampil untuk semua role) --}}
            <li class="relative" x-data="{ dropdownOpen: false }">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                    class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-white font-bold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ Auth::user()->initials }}
                </button>
                <div x-show="dropdownOpen" x-transition style="display: none;"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-50">
                    <div class="px-4 py-2">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <x-heroicon-o-user-circle class="w-5 h-5 mr-3 text-gray-400" />
                        <span>Profil Saya</span>
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 mr-3 text-gray-400" />
                            <span>Sign out</span>
                        </button>
                    </form>
                </div>
            </li>
        @endauth
    </ul>
</nav>
