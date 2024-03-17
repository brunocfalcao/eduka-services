<?php

namespace Eduka\Services\Mail\Orders;

use Eduka\Cube\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedForNewStudentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public User $student;

    public Order $order;

    public string $resetLink;

    public function __construct(User $student, Order $order, string $resetLink)
    {
        $this->student = $student;
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
            view: 'eduka-services::mail.new-order-for-new-student',
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
