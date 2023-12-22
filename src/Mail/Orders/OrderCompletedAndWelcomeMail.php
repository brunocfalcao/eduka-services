<?php

namespace Eduka\Services\Mail\Orders;

use Eduka\Cube\Models\Order;
use Eduka\Cube\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use League\CommonMark\CommonMarkConverter;

class OrderCompletedAndWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public User $user;

    public Order $order;

    public string $resetLink;

    public function __construct(User $user, Order $order, string $resetLink)
    {
        $this->user = $user;
        $this->order = $order;
        $this->resetLink = $resetLink;
    }

    public function envelope()
    {
        $address = new Address(
            eduka_mail_from($this->order->variant->course),
            eduka_mail_name($this->order->variant->course)
        );

        $subject = sprintf('Thanks for buying %s', $this->order->variant->course->name);

        $this->message = sprintf('# Thanks for buying %s !', $this->order->variant->course->name);
        $this->message .= PHP_EOL;
        $this->message .= PHP_EOL;
        $this->message .= 'Hi there,';
        $this->message .= PHP_EOL;
        $this->message = 'Reset link:'.$this->resetLink;

        /*
        $this->message = '# Thanks for subscribing!'.PHP_EOL;
        $this->message .= PHP_EOL; // Adding an extra newline for proper markdown separation
        $this->message .= 'Thanks a lot for subscribing to **'.$this->subscriber->course->name.'**!';
        $this->message .= PHP_EOL.PHP_EOL; // Adding an extra newline for proper markdown separation
        $this->message .= 'I will let you know when the course is launched, and will offer you a special discount coupon in return to your interest.';
        */

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        $converter = new CommonMarkConverter();

        return new Content(
            view: 'eduka-services::mail.new-order-and-welcome',
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
