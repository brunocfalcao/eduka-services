<?php

namespace Eduka\Services\Mail\Courses;

use Eduka\Cube\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseSaved extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
        $this->theme = 'eduka';
    }

    public function envelope()
    {
        $address = new Address(
            config('eduka.mail.from.email'),
            config('eduka.mail.from.name')
        );

        $action = $this->course->wasChanged() ? 'updated' : 'created';

        $subject = "Eduka system notification -- Course {$action}";

        $this->message = "Course {$this->course->name} {$action}";

        return new Envelope(from: $address, subject: $subject);
    }

    public function content()
    {
        return new Content(
            markdown: 'services::mail.system.single-message-notification',
        );
    }

    public function attachments()
    {
        return [];
    }
}
