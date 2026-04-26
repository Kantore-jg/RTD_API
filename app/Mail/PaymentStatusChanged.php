<?php

namespace App\Mail;

use App\Models\CompanyPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyPayment $payment,
        public string $status,
        public string $orgName,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->status === 'Validé' ? 'validé' : 'rejeté';

        return new Envelope(
            subject: "Votre paiement a été {$label} — RDT",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-status');
    }
}
