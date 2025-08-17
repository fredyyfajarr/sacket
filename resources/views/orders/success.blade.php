@extends('layouts.app')

@section('title', 'Payment Success')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        Payment Successful!
                    </h2>

                    <p class="text-gray-600 mb-6">
                        Your payment has been processed successfully.<br>
                        <strong>Order ID:</strong> {{ $order_id ?? '-' }} <br>
                        <strong>Status:</strong> {{ $status ?? 'paid' }} <br>
                        You will receive a confirmation email shortly.
                    </p>

                    <div class="space-y-3">
                        <a href="{{ route('orders.index') }}"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View My Tickets
                        </a>

                        <a href="{{ route('events.index') }}"
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Browse More Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
