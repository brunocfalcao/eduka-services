<?php

namespace Eduka\Services\Notifications\Subscribers;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Subscriber;
use Eduka\Services\Mail\Subscribers\SubscribedToCourseMail;

class SubscriberCreatedNotification extends EdukaNotification
{
    private Subscriber $subscriber;

    private Course $course;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function toMail($notifiable)
    {
        return (new SubscribedToCourseMail($this->subscriber))
            ->to($this->subscriber->email);
    }
}
