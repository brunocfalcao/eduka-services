<?php

namespace Eduka\Services\Mail\Subscribers;

use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Subscriber;
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

    public Course $course;

    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber, Course $course)
    {
        $this->subscriber = $subscriber;
        $this->course = $course;
    }

    public function envelope()
    {
        $address = new Address(
            eduka_mail_from($this->course),
            eduka_mail_name($this->course)
        );

        // @todo udpate
        $subject = sprintf('Thank You for Subscribing to Our %s Newsletter!', $this->course->name);

        $this->message = "Thank you for subscribing to our course newsletter! We're thrilled to have you as part of our exclusive community. Get ready to receive regular updates on our latest courses, industry insights, exclusive promotions, and invitations to special events. Welcome aboard!";

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
