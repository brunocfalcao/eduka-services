<?php

namespace Eduka\Services\Mail\Subscribers;

use Eduka\Cube\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscribedToCourseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function envelope()
    {
        $address = new Address(
            eduka_mail_from($this->subscriber->course),
            eduka_mail_name($this->subscriber->course)
        );

        $subject = sprintf('Thanks for subscribing to %s', $this->subscriber->course->name);

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        return new Content(
            view: 'course::mailables.new-subscriber',
            with: [
                'subscriber' => $this->subscriber,
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
