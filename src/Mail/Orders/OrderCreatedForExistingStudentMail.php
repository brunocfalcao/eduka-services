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

    public Student $student;

    public Order $order;

    public function __construct(Student $student, Order $order)
    {
        $this->student = $student;
        $this->order = $order;

        // Register the course view namespace, on the 'course' prefix.
        register_course_view_namespace($this->order->course);
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
        // Do we have a course view for this mailable?
        $view = view()->exists('course::new-order-existing-student') ?
                'course::mailables.new-order-existing-student' :
                'eduka::mailables.new-order-existing-student';

        return new Content(
            view: $view,
            with: [
                'order' => $this->order,
                'student' => $this->student,
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
