@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-900">My Tickets</h1>

    @if ($orders->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p class="font-bold">Oops!</p>
            <p>You haven't bought any tickets yet.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <div class="bg-white rounded-lg shadow-lg p-6 flex space-x-6">
                    <div class="flex-grow">
                        <h3 class="text-xl font-bold text-gray-800">{{ $order->event->name }}</h3>
                        <p class="text-gray-600 mt-1">
                            Order ID: <span class="font-semibold">{{ $order->order_number }}</span>
                        </p>
                        <p class="text-gray-600">
                            Status: <span class="font-semibold">{{ ucfirst($order->status) }}</span>
                        </p>
                        <p class="text-gray-600">
                            Total: <span
                                class="font-semibold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <a href="{{ route('orders.show', $order) }}"
                            class="inline-block px-4 py-2 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors duration-300">
                            View Order
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
