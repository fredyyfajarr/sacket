<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>E-Ticket - {{ $orderItem->unique_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
        }

        .ticket-container {
            width: 100%;
            border: 2px solid #333;
            padding: 25px;
            border-radius: 15px;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table td {
            padding: 10px 0;
            vertical-align: top;
        }

        .content-table td:first-child {
            font-weight: bold;
            width: 150px;
        }

        .qr-code {
            text-align: center;
            margin-top: 30px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <div class="header">
            <h1>E-TICKET</h1>
            <p>{{ $orderItem->order->event->name }}</p>
        </div>

        <table class="content-table">
            <tr>
                <td>Nama</td>
                <td>: {{ $orderItem->order->customer_name }}</td>
            </tr>
            <tr>
                <td>Kategori Tiket</td>
                <td>: {{ $orderItem->ticketCategory->name }}</td>
            </tr>
            <tr>
                <td>Tanggal Acara</td>
                <td>: {{ $orderItem->order->event->start_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td>: {{ $orderItem->order->event->location }}</td>
            </tr>
            <tr>
                <td style="font-size: 20px; padding-top: 20px;">KODE UNIK</td>
                <td style="font-size: 20px; padding-top: 20px;">: <strong>{{ $orderItem->unique_code }}</strong></td>
            </tr>
        </table>

        <div class="qr-code">
            {{-- Meng-embed gambar QR Code langsung ke PDF --}}
            <img src="data:image/png;base64,{{ base64_encode(QrCode::format('svg')->size(200)->generate($orderItem->unique_code)) }}"
                alt="QR Code">
        </div>

        <div class="footer">
            Terima kasih telah membeli tiket. Tunjukkan QR Code ini pada saat check-in.
        </div>
    </div>
</body>

</html>
