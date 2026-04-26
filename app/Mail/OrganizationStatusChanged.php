<?php

namespace App\Mail;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Organization $organization,
        public string $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->newStatus === 'active' ? 'réactivé' : 'suspendu';

        return new Envelope(
            subject: "Votre compte a été {$label} — RDT",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.organization-status');
    }
}
