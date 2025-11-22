<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment; // Wajib import ini
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf; // Wajib import ini

class TicketPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Tiket Anda: ' . $this->order->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-purchased',
        );
    }

    /**
     * Bagian ini yang melampirkan PDF ke email
     */
    public function attachments(): array
    {
        $attachments = [];

        // Kita loop semua item tiket dalam order tersebut
        // Jika beli 3 tiket, maka akan ada 3 file PDF terlampir
        foreach ($this->order->items as $item) {
            $pdf = Pdf::loadView('orders.pdf', ['orderItem' => $item]);

            $attachments[] = Attachment::fromData(
                fn () => $pdf->output(),
                'Tiket-' . $item->unique_code . '.pdf'
            )
            ->withMime('application/pdf');
        }

        return $attachments;
    }
}
