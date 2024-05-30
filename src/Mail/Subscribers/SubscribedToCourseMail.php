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

        // Register the course view namespace, on the 'course' prefix.
        register_course_view_namespace($this->subscriber->course);
    }

    public function envelope()
    {
        $address = new Address(
            $this->subscriber->course->admin->email,
            $this->subscriber->course->admin->name
        );

        $subject = sprintf('Thanks for subscribing to %s', $this->subscriber->course->name);

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        $view = eduka_view_or('course::mailables.new-subscriber');

        return new Content(
            view: $view,
            with: [
                'course' => $this->subscriber->course,
                'preview' => $this->subscriber->course->name.' - Thanks for subscribing!',
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
