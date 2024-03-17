<?php

namespace Eduka\Services\Mail\Orders;

use Eduka\Cube\Models\Order;
use Eduka\Cube\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedForExistingStudentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public User $student;

    public Order $order;

    public $url;

    public function __construct(Student $student, Order $order, string $url)
    {
        $this->student = $student;
        $this->order = $order;
        $this->url = $url;
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
            view: 'eduka-services::mail.new-order-for-existing-student',
            with: [
                'order' => $this->order,
                'url' => $this->url,
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
