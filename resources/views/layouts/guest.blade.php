<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sacket'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased flex flex-col min-h-screen bg-gray-100">

    {{-- Menggunakan Header Utama Kita --}}
    @include('components.header')

    {{-- Konten Form di Tengah Halaman --}}
    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>

    {{-- Menggunakan Footer Utama Kita --}}
    @include('components.footer')

    {{-- Menambahkan stack untuk script khusus halaman --}}
    @stack('scripts')
</body>

</html>
