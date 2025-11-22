<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse ($events as $event)
        <div
            class="bg-white rounded-lg shadow-lg overflow-hidden group transform hover:-translate-y-2 transition-transform duration-300">
            <a href="{{ route('events.show', $event->slug) }}" class="block">
                <div class="relative">
                    {{-- Gambar --}}
                    <img src="{{ asset($event->image) }}" alt="Gambar {{ $event->name }}"
                        class="w-full h-48 object-cover">

                    {{-- Overlay Hover --}}
                    <div
                        class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-40 transition-all duration-300">
                    </div>

                    {{-- Badge Harga Termurah (Opsional - Pemanis) --}}
                    @if ($event->ticketCategories->isNotEmpty())
                        <div
                            class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                            Mulai Rp {{ number_format($event->ticketCategories->min('price'), 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 truncate">{{ $event->name }}</h3>

                    {{-- Tanggal --}}
                    <p class="text-gray-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ $event->start_date->format('d F Y') }}
                    </p>

                    {{-- Lokasi --}}
                    <p class="text-gray-600 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        {{-- Empty State Keren --}}
        <div class="col-span-full text-center py-16">
            <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Tidak ada event ditemukan</h3>
            <p class="mt-1 text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
        </div>
    @endforelse
</div>

{{-- Pagination (Penting: gunakan appends request agar filter tidak hilang saat ganti halaman) --}}
<div class="mt-12">
    {{ $events->appends(request()->except('page'))->links() }}
</div>
