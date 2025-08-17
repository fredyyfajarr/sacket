@extends('layouts.app')

@section('title', 'Ticket Scanner')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 text-center">Ticket Scanner</h1>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg">
            <div class="p-6 text-gray-900">

                {{-- Area untuk menampilkan kamera --}}
                <div id="reader" class="w-full rounded-lg overflow-hidden"></div>

                {{-- Area untuk menampilkan hasil scan --}}
                <div id="result" class="mt-6 p-4 rounded-lg text-center font-bold text-lg hidden">
                    <p id="result-message"></p>
                    <div id="ticket-details" class="mt-2 text-sm font-normal"></div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        // Kode JavaScript lengkap untuk scanner (tidak ada yang berubah di sini)
        document.addEventListener('DOMContentLoaded', function() {
            const resultContainer = document.getElementById('result');
            const resultMessage = document.getElementById('result-message');
            const ticketDetails = document.getElementById('ticket-details');
            let lastScanTime = 0;

            function onScanSuccess(decodedText, decodedResult) {
                const now = new Date().getTime();
                if (now - lastScanTime < 3000) {
                    return;
                }
                lastScanTime = now;
                const audio = new Audio(
                    'https://cdn.jsdelivr.net/gh/scottschiller/SoundManager2@master/demo/mp3/beep-1.mp3');
                audio.play();
                verifyTicket(decodedText);
            }

            async function verifyTicket(uniqueCode) {
                try {
                    const response = await fetch("{{ route('admin.ticket.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            unique_code: uniqueCode
                        })
                    });
                    const data = await response.json();
                    displayResult(data.status, data.message, data);
                } catch (error) {
                    displayResult('error', 'Koneksi Gagal!');
                }
            }

            function displayResult(status, message, data = null) {
                resultContainer.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100',
                    'text-red-800');
                if (status === 'success') {
                    resultContainer.classList.add('bg-green-100', 'text-green-800');
                } else {
                    resultContainer.classList.add('bg-red-100', 'text-red-800');
                }
                resultMessage.textContent = message;
                if (data && data.data) {
                    ticketDetails.innerHTML =
                        `<p><strong>${data.data.order.event.name}</strong></p><p>${data.data.ticket_category.name}</p><p>Atas Nama: ${data.data.order.customer_name}</p>`;
                } else {
                    ticketDetails.innerHTML = '';
                }
                setTimeout(() => {
                    resultContainer.classList.add('hidden');
                }, 5000);
            }

            const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            }, false);
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
@endpush
