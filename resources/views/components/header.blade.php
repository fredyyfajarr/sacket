{{-- Kita gunakan Alpine.js untuk mengelola state buka/tutup menu mobile dan efek scroll --}}
<header x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="{ 'bg-white shadow-md': scrolled, 'bg-transparent': !scrolled }">

    <div class="container mx-auto p-4 flex justify-between items-center">
        {{-- Logo --}}
        <a href="{{ route('events.index') }}" class="text-2xl font-bold"
            :class="{ 'text-gray-800': scrolled, 'text-gray-500': !scrolled }">
            Sacket
        </a>

        {{-- Navigasi Desktop (tersembunyi di mobile) --}}
        <div class="hidden md:block">
            @include('components.nav-links')
        </div>

        {{-- Tombol Hamburger (hanya terlihat di mobile) --}}
        <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="focus:outline-none"
                :class="{ 'text-gray-800': scrolled, 'text-white': !scrolled }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Menu Mobile (muncul saat tombol hamburger diklik) --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4" @click.away="mobileMenuOpen = false"
        class="md:hidden bg-white shadow-lg absolute top-full left-0 right-0">
        @include('components.nav-links', ['isMobile' => true])
    </div>
</header>
