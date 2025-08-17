@extends('layouts.app')

@section('title', 'Sacket - Temukan Event Favoritmu')

@section('content')

    <div class="mb-16">
        <div id="banner-carousel" class="splide" aria-label="Event Unggulan">
            <div class="splide__track">
                <ul class="splide__list">

                    @forelse($popularEvents as $event)
                        <li class="splide__slide">
                            <div
                                class="p-8 md:p-12 flex flex-col md:flex-row items-center justify-center bg-gray-50 rounded-lg">
                                <div class="w-full md:w-1/2 mb-6 md:mb-0">
                                    <a href="{{ route('events.show', $event->slug) }}">
                                        <img src="{{ asset($event->image) }}" alt="Banner {{ $event->name }}"
                                            class="rounded-lg shadow-2xl w-full h-auto max-h-80 object-contain">
                                    </a>
                                </div>

                                <div class="w-full md:w-1/2 md:pl-12 text-center md:text-left">
                                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-800">{{ $event->name }}</h2>
                                    <p class="text-lg text-gray-600 mt-2">{{ $event->start_date->format('d F Y') }} di
                                        {{ $event->location }}</p>
                                    <a href="{{ route('events.show', $event->slug) }}"
                                        class="inline-block mt-6 bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:bg-blue-700 transition-colors duration-300">
                                        Beli Tiket Sekarang
                                    </a>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="splide__slide">
                            <div class="w-full text-center py-12 bg-gray-50 rounded-lg">
                                <p class="text-gray-500 text-xl">Belum ada event unggulan saat ini.</p>
                            </div>
                        </li>
                    @endforelse

                </ul>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- BAGIAN GALERI EVENT LAINNYA (TIDAK BERUBAH) --}}
    {{-- ====================================================== --}}
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800">Temukan Event Lainnya</h1>
        <p class="text-lg text-gray-600 mt-2">Jelajahi berbagai acara musik menarik dan dapatkan tiketmu sekarang!</p>
    </div>

    {{-- ====================================================== --}}
    {{-- FORM PENCARIAN & FILTER --}}
    {{-- ====================================================== --}}
    <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
        <form action="{{ route('events.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            {{-- Kolom Pencarian --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Nama Event</label>
                <input type="text" name="search" id="search" placeholder="Contoh: Jazz Festival"
                    value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            {{-- Kolom Filter Lokasi --}}
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Filter Lokasi</label>
                <select name="location" id="location"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                            {{ $location }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Aksi --}}
            <div class="md:col-span-3 flex justify-end space-x-2">
                <a href="{{ route('events.index') }}"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Reset</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
    {{-- ====================================================== --}}
    {{-- AKHIR DARI FORM --}}
    {{-- ====================================================== --}}

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($events as $event)
            <div
                class="bg-white rounded-lg shadow-lg overflow-hidden group transform hover:-translate-y-2 transition-transform duration-300">
                <a href="{{ route('events.show', $event->slug) }}" class="block">
                    <div class="relative">
                        <img src="{{ asset($event->image) }}" alt="Gambar {{ $event->name }}"
                            class="w-full h-48 object-cover">
                        <div
                            class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-40 transition-all duration-300">
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 truncate">{{ $event->name }}</h3>
                        <p class="text-gray-600 mt-2">
                            <svg class="w-4 h-4 inline-block mr-1 -mt-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ $event->start_date->format('d F Y') }}
                        </p>
                        <p class="text-gray-600 mt-1">
                            <svg class="w-4 h-4 inline-block mr-1 -mt-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $event->location }}
                        </p>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500 text-xl">Belum ada event yang akan datang saat ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $events->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek jika ada slide di dalam carousel
            if (document.querySelectorAll('#banner-carousel .splide__slide').length > 0) {
                new Splide('#banner-carousel', {
                    type: 'loop', // Membuat carousel berputar (looping)
                    perPage: 1, // Hanya tampilkan 1 slide per halaman
                    autoplay: true, // Otomatis berjalan
                    interval: 5000, // Pindah slide setiap 5 detik
                    pagination: true, // Menampilkan indikator titik (dots)
                    arrows: true, // Menampilkan tombol panah navigasi
                    pauseOnHover: true, // Berhenti saat mouse diarahkan ke slide
                }).mount();
            }
        });
    </script>
@endpush
