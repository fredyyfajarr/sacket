@extends('layouts.app')

@section('title', 'Sacket - Temukan Event Favoritmu')

@section('content')

    {{-- ====================================================== --}}
    {{-- BAGIAN BANNER (CAROUSEL) - TIDAK BERUBAH --}}
    {{-- ====================================================== --}}
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

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800">Temukan Event Lainnya</h1>
        <p class="text-lg text-gray-600 mt-2">Jelajahi berbagai acara musik menarik dan dapatkan tiketmu sekarang!</p>
    </div>

    {{-- ====================================================== --}}
    {{-- FORM LIVE SEARCH & FILTER --}}
    {{-- ====================================================== --}}
    <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
        {{-- Form tanpa action/method karena di-handle JS --}}
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" onsubmit="event.preventDefault();">

            {{-- 1. Search --}}
            <div class="md:col-span-4 lg:col-span-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Event</label>
                <input type="text" id="search" placeholder="Nama event..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            {{-- 2. Lokasi --}}
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <select id="location"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Harga (Budget) --}}
            <div>
                <label for="price_max" class="block text-sm font-medium text-gray-700 mb-1">Budget Maksimal</label>
                <select id="price_max"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Harga</option>
                    <option value="100000">Di bawah 100rb</option>
                    <option value="300000">Di bawah 300rb</option>
                    <option value="500000">Di bawah 500rb</option>
                    <option value="1000000">Di bawah 1 Juta</option>
                </select>
            </div>

            {{-- 4. Sorting --}}
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
                <select id="sort"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="latest">Terbaru Diupload</option>
                    <option value="soonest">Waktu Terdekat</option>
                    <option value="cheapest">Harga Termurah</option>
                </select>
            </div>
        </form>
    </div>

    {{-- INDICATOR LOADING --}}
    <div id="loading" class="hidden text-center py-12">
        <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <p class="mt-2 text-gray-500">Sedang mencari...</p>
    </div>

    {{-- CONTAINER HASIL PENCARIAN (Diisi Partial View) --}}
    <div id="events-container">
        {{-- Default load (tanpa filter) --}}
        @include('events.partials.list')
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Splide Carousel Setup
            if (document.querySelectorAll('#banner-carousel .splide__slide').length > 0) {
                new Splide('#banner-carousel', {
                    type: 'loop',
                    perPage: 1,
                    autoplay: true,
                    interval: 5000,
                    pagination: true,
                    arrows: true,
                    pauseOnHover: true,
                }).mount();
            }

            // 2. Live Search Logic
            const inputs = ['search', 'location', 'price_max', 'sort'];
            const container = document.getElementById('events-container');
            const loading = document.getElementById('loading');
            let debounceTimer;

            function fetchEvents() {
                // UI Loading State
                loading.classList.remove('hidden');
                container.classList.add('opacity-50');

                // Ambil value dari semua input
                const params = new URLSearchParams();
                inputs.forEach(id => {
                    const val = document.getElementById(id).value;
                    if (val) params.append(id, val);
                });

                // Fetch AJAX
                fetch(`{{ route('events.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        container.innerHTML = html;
                        loading.classList.add('hidden');
                        container.classList.remove('opacity-50');
                    })
                    .catch(err => {
                        console.error(err);
                        loading.classList.add('hidden');
                        container.classList.remove('opacity-50');
                    });
            }

            // Pasang Event Listener untuk setiap input
            inputs.forEach(id => {
                document.getElementById(id).addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(fetchEvents, 500); // Delay 500ms
                });
            });
        });
    </script>
@endpush
