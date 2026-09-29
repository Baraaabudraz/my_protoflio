<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notifies the site owner about a new inquiry. Replying goes straight to the client.
 */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone: ?string, service: ?string, budget: ?string, message: string, locale: string}  $inquiry
     */
    public function __construct(public array $inquiry, public int $messageId) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->inquiry['email'], $this->inquiry['name'])],
            subject: 'New inquiry from '.$this->inquiry['name'].($this->inquiry['service'] ? ' — '.$this->inquiry['service'] : ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-received');
    }
}
