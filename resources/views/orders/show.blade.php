@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="max-w-xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">Order Details</h1>

        @if ($order)
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800">{{ $order->event->name }}</h2>
                <p class="text-lg text-gray-600 mt-2">Order ID: <span class="font-semibold">{{ $order->order_number }}</span>
                </p>

                <hr class="my-6">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500">Customer Name:</p>
                        <p class="font-semibold">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Event Date:</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($order->event->start_date)->format('d F Y H:i') }}
                        </p>
                    </div>
                </div>

                <h3 class="text-xl font-bold mt-8 mb-4">Tickets</h3>
                <div class="space-y-4">
                    @foreach ($order->items as $item)
                        <div class="bg-gray-100 p-4 rounded-lg flex justify-between items-center">
                            <div>
                                <p class="font-semibold">{{ $item->ticketCategory->name }}</p>
                                <p class="text-sm text-gray-600">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500 mt-2">Unique Code: {{ $item->unique_code }}</p>
                            </div>
                            <a href="{{ route('orders.download', $item) }}"
                                class="px-4 py-2 text-sm font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors duration-300">
                                Download PDF
                            </a>
                        </div>

                        <div class="mt-4 text-center">
                            <div class="bg-white p-4 rounded-lg inline-block shadow-sm">
                                {!! QrCode::size(150)->generate($item->unique_code) !!}
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Scan to verify</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>Order not found.</p>
            </div>
        @endif
    </div>
@endsection
