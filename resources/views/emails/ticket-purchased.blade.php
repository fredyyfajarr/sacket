<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Berhasil</title>
    <style>
        body { font-family: sans-serif; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Pembayaran Anda Berhasil!</h1>
    <p>Halo {{ $order->customer_name }},</p>
    <p>Terima kasih. Pembayaran Anda untuk event <strong>{{ $order->event->name }}</strong> telah kami konfirmasi.</p>
    <p>E-Ticket Anda sekarang sudah tersedia di akun Anda. Silakan unduh melalui tombol "My Tickets" di website kami.</p>

    <a href="{{ route('orders.index') }}" class="button" style="color: #ffffff;">Lihat Tiket Saya</a>

    <br>
    <p>Terima kasih,</p>
    <p>Tim Sacket</p>
</body>
</html>
