<?php

namespace App\Mail;

use App\Models\Signup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignupConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Signup $signup,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bevestiging van je PinoCrew-inschrijving',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.signup-confirmation',
        );
    }
}