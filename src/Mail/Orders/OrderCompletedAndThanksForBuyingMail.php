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

class OrderCompletedAndThanksForBuyingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public User $user;

    public Order $order;

    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }

    public function envelope()
    {
        $address = new Address(
            eduka_mail_from($this->order->course),
            eduka_mail_name($this->order->course)
        );

        $subject = sprintf('Thanks for buying %s', $this->order->course->name);

        $this->message = sprintf('# Thanks for buying %s !', $this->order->variant->course->name);
        $this->message .= PHP_EOL;
        $this->message .= PHP_EOL;
        $this->message .= 'Hi there,';
        $this->message .= PHP_EOL;
        $this->message = 'Thanks for buying one more course!';

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
