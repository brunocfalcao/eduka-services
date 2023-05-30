<?php

namespace Eduka\Services\Mail\Domains;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscribeToNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public function __construct() {}

    public function envelope()
    {
        $address = new Address(
            config('eduka.mail.from.email'),
            config('eduka.mail.from.name')
        );

        // @todo udpate
        $subject = "Mastering Nova Newsletter";

        $this->message = "Welcome to the Mastering Nova Newsletter. We'll keep you updated.";

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        return new Content(
            markdown: 'services::mail.newsletter.subscribed-to-newsletter',
        );
    }

    public function attachments()
    {
        return [];
    }
}
