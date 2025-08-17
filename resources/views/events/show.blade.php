@extends('layouts.app')

@section('title', $event->name)

@section('content')
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <img src="{{ asset($event->image) }}" alt="{{ $event->name }}" class="w-full h-64 md:h-96 object-cover">

        <div class="p-6 md:p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $event->name }}</h1>
            <p class="text-lg text-gray-600 mt-2">
                Tanggal: {{ $event->start_date->format('d F Y') }}
                @if ($event->start_date->format('d F Y') !== $event->end_date->format('d F Y'))
                    - {{ $event->end_date->format('d F Y') }}
                @endif
            </p>
            <p class="text-lg text-gray-600">Lokasi: {{ $event->location }}</p>
            <div class="prose max-w-none text-gray-700 mt-4">
                {!! $event->description !!}
            </div>

            <hr class="my-8">

            {{-- Menampilkan error validasi dari backend --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                    <p class="font-bold">Oops! Ada yang salah:</p>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM PEMBELIAN BARU DENGAN ALPINE.JS --}}
            <div x-data="{
                quantity: 1,
                ticketPrice: 0,
                promoCode: '',
                promoError: '',
                promoSuccess: '',
                discount: 0,
                subtotal: 0,
                finalPrice: 0,
                appliedPromo: null,
                loading: false,
            
                updateTicketPrice(event) {
                    this.ticketPrice = parseFloat(event.target.options[event.target.selectedIndex].dataset.price) || 0;
                    this.resetPromo();
                    this.calculateFinalPrice();
                },
            
                async validatePromo() {
                    this.resetPromo();
                    if (!this.promoCode) { this.promoError = 'Silakan masukkan kode promo.'; return; }
                    this.loading = true;
            
                    try {
                        const response = await fetch('{{ route('promo.validate') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ code: this.promoCode })
                        });
                        const data = await response.json();
            
                        if (data.valid) {
                            this.promoSuccess = data.message;
                            this.appliedPromo = data.promo_code;
                        } else {
                            this.promoError = data.message;
                        }
                    } catch (e) {
                        this.promoError = 'Gagal memvalidasi kode. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                        this.calculateFinalPrice();
                    }
                },
            
                calculateFinalPrice() {
                    this.subtotal = this.quantity * this.ticketPrice;
                    let calculatedDiscount = 0;
                    if (this.appliedPromo) {
                        if (this.appliedPromo.type === 'percentage') {
                            calculatedDiscount = this.subtotal * (this.appliedPromo.value / 100);
                        } else {
                            calculatedDiscount = parseFloat(this.appliedPromo.value);
                        }
                    }
                    this.discount = Math.min(calculatedDiscount, this.subtotal);
                    this.finalPrice = this.subtotal - this.discount;
                },
            
                resetPromo() {
                    this.promoError = '';
                    this.promoSuccess = '';
                    this.discount = 0;
                    this.appliedPromo = null;
                },
            
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
                }
            }" @input.debounce.500ms="calculateFinalPrice()">

                <h2 class="text-2xl font-bold text-gray-900 mb-4">Beli Tiket</h2>

                <form action="{{ route('orders.pay', $event) }}" method="POST" class="space-y-6">
                    @csrf
                    {{-- Input tersembunyi untuk mengirim kode promo yang valid ke backend --}}
                    <input type="hidden" name="promo_code" :value="appliedPromo ? promoCode : ''">

                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="customer_email" name="customer_email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label for="ticket_category_id" class="block text-sm font-medium text-gray-700">Kategori
                            Tiket</label>
                        <select name="ticket_category_id" id="ticket_category_id" @change="updateTicketPrice($event)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                            <option value="" data-price="0">-- Pilih Kategori Tiket --</option>
                            @foreach ($event->ticketCategories as $category)
                                <option value="{{ $category->id }}" data-price="{{ $category->price }}">
                                    {{ $category->name }} (Rp{{ number_format($category->price, 0, ',', '.') }}) - Sisa
                                    {{ $category->stock }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Tiket</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="10"
                            x-model.number.debounce="quantity"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    {{-- KOLOM KODE PROMO BARU --}}
                    <div>
                        <label for="promo_code" class="block text-sm font-medium text-gray-700">Kode Promo
                            (Opsional)</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="promo_code" x-model.debounce.500ms="promoCode"
                                @keydown.enter.prevent="validatePromo" placeholder="Masukkan Kode Promo"
                                class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" @click="validatePromo" :disabled="loading"
                                class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <span x-show="!loading">Terapkan</span>
                                <span x-show="loading">...</span>
                            </button>
                        </div>
                        <p x-show="promoSuccess" x-text="promoSuccess" class="text-sm text-green-600 mt-2"></p>
                        <p x-show="promoError" x-text="promoError" class="text-sm text-red-600 mt-2"></p>
                    </div>

                    {{-- RINGKASAN BIAYA DINAMIS --}}
                    <div class="space-y-2 py-4 border-t border-b border-gray-200">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span> <span
                                x-text="formatCurrency(subtotal)">Rp 0</span></div>
                        <div x-show="discount > 0" class="flex justify-between text-green-600"><span>Diskon</span> <span
                                x-text="'- ' + formatCurrency(discount)"></span></div>
                        <div class="flex justify-between font-bold text-lg text-gray-900"><span>Total</span> <span
                                x-text="formatCurrency(finalPrice)">Rp 0</span></div>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-semibold py-3 rounded-md shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                        Lanjutkan ke Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
