<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirms to the client that their inquiry arrived, in the language they used on the site.
 */
class ContactMessageConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone: ?string, service: ?string, budget: ?string, message: string, locale: string}  $inquiry
     * @param  array{name: string, email: string, whatsapp: string, site_url: string}  $owner
     */
    public function __construct(public array $inquiry, public array $owner)
    {
        $this->locale($inquiry['locale']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: $this->owner['email'] ? [new Address($this->owner['email'], $this->owner['name'])] : [],
            subject: __('Thanks for your message, :name', ['name' => $this->inquiry['name']]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-confirmation');
    }
}
