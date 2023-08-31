<?php

namespace Eduka\Services\Notifications\Subscribers;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Subscriber;
use Eduka\Services\Mail\Subscribers\SubscribeToNewsletter;

class SubscribedToNewsletter extends EdukaNotification
{
    private Subscriber $subscriber;

    private Course $course;

    public function __construct(Subscriber $subscriber, Course $course)
    {
        $this->subscriber = $subscriber;
        $this->course = $course;
    }

    public function toMail($notifiable)
    {
        // @todo find a better name
        return (new SubscribeToNewsletter($this->subscriber, $this->course))
                ->to(config('eduka.mail.to.email'));
    }
}
