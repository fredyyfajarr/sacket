@extends('layouts.app')

@section('title', 'Live Ticket Scanner')

@section('content')
    <div class="max-w-xl mx-auto">
        <h1 class="text-3xl font-bold mb-4 text-gray-900 text-center">Live Scanner</h1>

        {{-- Pilihan Event --}}
        <div class="mb-4 bg-white p-4 rounded-lg shadow">
            <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Event yang sedang
                berlangsung:</label>
            <select id="event_id"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih Event --</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg relative">
            {{-- Loading Indicator --}}
            <div id="loading-scan"
                class="hidden absolute inset-0 bg-white bg-opacity-80 z-10 flex items-center justify-center">
                <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>

            <div class="p-4">
                {{-- Area Kamera --}}
                <div id="reader" class="w-full rounded-lg overflow-hidden bg-black" style="min-height: 300px;"></div>

                {{-- Kontrol Kamera --}}
                <div class="mt-4 flex justify-center gap-2" id="camera-controls">
                    <button id="start-camera" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Mulai Kamera
                    </button>
                    <button id="stop-camera" class="hidden bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Stop
                    </button>
                </div>

                {{-- Area Hasil Scan --}}
                <div id="result" class="mt-6 p-4 rounded-lg text-center hidden border-2">
                    <div class="text-4xl mb-2" id="result-icon"></div>
                    <h2 id="result-message" class="font-bold text-xl uppercase"></h2>
                    <div id="ticket-details" class="mt-2 text-sm text-gray-700"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Library QR Code --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("reader");
            const startBtn = document.getElementById('start-camera');
            const stopBtn = document.getElementById('stop-camera');
            const eventSelect = document.getElementById('event_id');

            const resultContainer = document.getElementById('result');
            const resultMessage = document.getElementById('result-message');
            const resultIcon = document.getElementById('result-icon');
            const ticketDetails = document.getElementById('ticket-details');
            const loadingScan = document.getElementById('loading-scan');

            let isScanning = false;
            let isProcessing = false; // Mencegah scan ganda saat loading API

            // Konfigurasi Kamera
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0
            };

            // Audio (Success / Fail)
            const beepSuccess = new Audio(
                'https://cdn.pixabay.com/audio/2022/03/15/audio_2b436202d0.mp3'); // Suara "Ding"
            const beepFail = new Audio(
                'https://cdn.pixabay.com/audio/2021/08/04/audio_0625c1539c.mp3'); // Suara "Buzzer"

            // 1. FUNGSI MULAI KAMERA
            startBtn.addEventListener('click', () => {
                if (!eventSelect.value) {
                    alert('Mohon pilih Event terlebih dahulu!');
                    return;
                }

                // Gunakan kamera belakang (environment)
                html5QrCode.start({
                        facingMode: "environment"
                    }, config, onScanSuccess)
                    .then(() => {
                        isScanning = true;
                        startBtn.classList.add('hidden');
                        stopBtn.classList.remove('hidden');
                        resultContainer.classList.add('hidden');
                        eventSelect.disabled = true; // Kunci dropdown saat scan berjalan
                    })
                    .catch(err => {
                        console.error("Gagal menyalakan kamera", err);
                        alert("Gagal akses kamera. Pastikan izin diberikan.");
                    });
            });

            // 2. FUNGSI STOP KAMERA
            stopBtn.addEventListener('click', () => {
                html5QrCode.stop().then(() => {
                    isScanning = false;
                    startBtn.classList.remove('hidden');
                    stopBtn.classList.add('hidden');
                    eventSelect.disabled = false;
                }).catch(err => console.error(err));
            });

            // 3. SAAT QR TERDETEKSI
            function onScanSuccess(decodedText, decodedResult) {
                if (isProcessing) return; // Jangan scan jika sedang proses verifikasi sebelumnya

                isProcessing = true;
                loadingScan.classList.remove('hidden'); // Tampilkan spinner loading

                // Pause sebentar kameranya biar gak pusing
                html5QrCode.pause();

                verifyTicket(decodedText);
            }

            // 4. KIRIM KE BACKEND
            async function verifyTicket(uniqueCode) {
                try {
                    // PERBAIKAN: Gunakan path relatif "/ticket/verify" agar support Ngrok/HTTPS
                    const response = await fetch("/ticket/verify", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            unique_code: uniqueCode,
                            event_id: eventSelect.value
                        })
                    });
                    const data = await response.json();
                    displayResult(data.status, data.message, data);

                    // Mainkan suara
                    if (data.status === 'success') beepSuccess.play();
                    else beepFail.play();

                } catch (error) {
                    displayResult('error', 'Koneksi Server Gagal!', {
                        detail: error.message
                    });
                    beepFail.play();
                } finally {
                    isProcessing = false;
                    loadingScan.classList.add('hidden');

                    // Resume kamera setelah 2 detik agar petugas bisa baca hasil dulu
                    setTimeout(() => {
                        if (isScanning) html5QrCode.resume();
                    }, 2500);
                }
            }

            // 5. TAMPILKAN HASIL
            function displayResult(status, message, data = null) {
                resultContainer.classList.remove('hidden', 'border-green-500', 'bg-green-50', 'border-red-500',
                    'bg-red-50', 'border-yellow-500', 'bg-yellow-50');
                resultContainer.classList.add('block');

                if (status === 'success') {
                    // BERHASIL
                    resultContainer.classList.add('border-green-500', 'bg-green-50', 'text-green-800');
                    resultIcon.innerHTML = '✅';
                    ticketDetails.innerHTML = `
                        <div class="mt-2 p-2 bg-white rounded shadow-sm">
                            <p class="font-bold text-lg">${data.data.ticket_category.name}</p>
                            <p class="text-gray-600">${data.data.order.customer_name}</p>
                            <p class="text-xs text-gray-400 mt-1">Scan: ${data.checked_in_at}</p>
                        </div>
                    `;
                } else {
                    // GAGAL
                    resultContainer.classList.add('border-red-500', 'bg-red-50', 'text-red-800');
                    resultIcon.innerHTML = '❌';

                    let detailHtml = '';
                    if (data && data.detail) detailHtml = `<p class="font-bold text-red-600">${data.detail}</p>`;

                    // Jika tiket ditemukan tapi error (misal sudah dipakai)
                    if (data && data.data) {
                        detailHtml += `
                            <div class="mt-2 p-2 bg-white rounded shadow-sm opacity-75">
                                <p class="text-sm">Pemilik: ${data.data.order.customer_name}</p>
                                <p class="text-sm">Kategori: ${data.data.ticket_category.name}</p>
                                ${data.checked_in_at ? `<p class="text-xs text-red-500">Sudah masuk jam: ${data.checked_in_at}</p>` : ''}
                            </div>
                        `;
                    }

                    ticketDetails.innerHTML = detailHtml;
                }

                resultMessage.textContent = message;
            }
        });
    </script>
@endpush
