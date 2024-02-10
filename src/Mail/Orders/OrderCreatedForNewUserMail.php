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

class OrderCreatedForNewUserMail extends Mailable
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
            eduka_mail_from($this->order->course),
            eduka_mail_name($this->order->course)
        );

        $subject = sprintf('Thanks for buying %s', $this->order->course->name);

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        return new Content(
            view: 'eduka-services::mail.new-order-for-new-user',
            with: [
                'order' => $this->order,
                'resetLink' => $this->resetLink,
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
