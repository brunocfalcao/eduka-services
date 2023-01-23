<?php

namespace Eduka\Services\Mail\Domains;

use Eduka\Cube\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DomainSaved extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
        $this->theme = 'eduka';
    }

    public function envelope()
    {
        $address = new Address(
            config('eduka.mail.from.email'),
            config('eduka.mail.from.name')
        );

        $action = $this->domain->wasChanged() ? 'updated' : 'created';

        $subject = "Eduka system notification -- Domain {$action}";

        $this->message = "Domain {$this->domain->suffix} {$action}";

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        return new Content(
            markdown: 'services::mail.system.single-message-notification',
        );
    }

    public function attachments()
    {
        return [];
    }
}
