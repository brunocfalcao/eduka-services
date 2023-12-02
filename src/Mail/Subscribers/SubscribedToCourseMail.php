<?php

namespace Eduka\Services\Mail\Subscribers;

use Eduka\Cube\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use League\CommonMark\CommonMarkConverter;

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

        $this->message = '# Thanks for subscribing!'.PHP_EOL;
        $this->message .= PHP_EOL; // Adding an extra newline for proper markdown separation
        $this->message .= 'Thanks a lot for subscribing to **'.$this->subscriber->course->name.'**!';
        $this->message .= PHP_EOL.PHP_EOL; // Adding an extra newline for proper markdown separation
        $this->message .= 'I will let you know when the course is launched, and will offer you a special discount coupon in return to your interest.';

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        $converter = new CommonMarkConverter();

        return new Content(
            view: 'eduka-services::mail.subscribed-to-course',
            with: [
                'content' => $converter->convertToHtml($this->message),
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
