<?php

namespace App\Mail;

use App\Models\Signup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShiftReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Signup $signup,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Herinnering: morgen heb je een PinoCrew-dienst',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.shift-reminder',
        );
    }
}