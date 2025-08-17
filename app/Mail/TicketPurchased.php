<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        // Kita hanya perlu memuat relasi event untuk nama event di subjek email
        $this->order->loadMissing('event');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil untuk ' . $this->order->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-purchased', // Tetap menggunakan view yang sama
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Kembalikan array kosong karena tidak ada lampiran
        return [];
    }
}
