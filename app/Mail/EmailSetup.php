<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EmailSetup extends Mailable
{
    public function __construct(
        public array $users,
        public array $totals,
        public string $emailSubject,
        public string $titleText,
        public string $heroImageUrl,
        public string $template
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->template,
        );
    }
}

