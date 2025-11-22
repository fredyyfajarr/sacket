<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ticket {{ $orderItem->unique_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .ticket {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #1f2937;
            /* Dark Gray */
            color: white;
            padding: 20px 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            margin: 5px 0 0;
            opacity: 0.8;
            font-size: 14px;
        }

        .body {
            padding: 30px;
            display: table;
            width: 100%;
        }

        .info {
            display: table-cell;
            vertical-align: top;
            width: 65%;
            padding-right: 20px;
            border-right: 2px dashed #e5e7eb;
        }

        .qr-section {
            display: table-cell;
            vertical-align: middle;
            width: 35%;
            text-align: center;
            padding-left: 20px;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 20px;
            display: block;
        }

        .row {
            display: table;
            width: 100%;
        }

        .col {
            display: table-cell;
            width: 50%;
        }

        .footer {
            background-color: #f9fafb;
            padding: 15px 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #dbeafe;
            color: #1e40af;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">
            <h1>{{ $orderItem->order->event->name }}</h1>
            <p>E-TICKET / ACCESS PASS</p>
        </div>

        <div class="body">
            <div class="info">
                <div class="label">NAMA PENGUNJUNG</div>
                <div class="value">{{ strtoupper($orderItem->order->customer_name) }}</div>

                <div class="row">
                    <div class="col">
                        <div class="label">TANGGAL & WAKTU</div>
                        <div class="value">
                            {{ $orderItem->order->event->start_date->format('d M Y') }}<br>
                            <small
                                style="font-weight: normal;">{{ $orderItem->order->event->start_date->format('H:i') }}
                                WIB</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="label">LOKASI</div>
                        <div class="value">{{ $orderItem->order->event->location }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="label">JENIS TIKET</div>
                        <div class="value">
                            <span class="badge">{{ strtoupper($orderItem->ticketCategory->name) }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="label">ORDER ID</div>
                        <div class="value">#{{ $orderItem->order->order_number }}</div>
                    </div>
                </div>
            </div>

            <div class="qr-section">
                <img src="data:image/png;base64,{{ base64_encode(QrCode::format('svg')->size(150)->margin(1)->generate($orderItem->unique_code)) }}"
                    alt="QR Code">
                <div
                    style="margin-top: 10px; font-family: monospace; font-weight: bold; font-size: 14px; letter-spacing: 1px;">
                    {{ $orderItem->unique_code }}
                </div>
                <div style="font-size: 10px; color: #6b7280; margin-top: 5px;">
                    Scan di pintu masuk
                </div>
            </div>
        </div>

        <div class="footer">
            Tiket ini adalah bukti pembayaran yang sah. Dilarang menggandakan tiket ini.
            Satu tiket hanya berlaku untuk satu kali masuk.
        </div>
    </div>
</body>

</html>
