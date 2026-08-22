<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredencialesRecuperadas extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Usuario $usuario,
        public string $password,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de credenciales',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.credenciales-recuperadas',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
