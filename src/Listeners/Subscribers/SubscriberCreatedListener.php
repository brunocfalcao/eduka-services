<?php

namespace Eduka\Services\Listeners\Subscribers;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Subscribers\SubscriberCreatedEvent;
use Eduka\Services\Mail\Subscribers\SubscribedToCourseMail;
use Illuminate\Support\Facades\Mail;

class SubscriberCreatedListener extends EdukaListener
{
    public function handle(SubscriberCreatedEvent $event)
    {
        // Register the course view namespace, on the 'course' prefix.
        push_course_view_namespace($event->subscriber->course);

        // Register the storage eduka disk.
        push_eduka_filesystem_disk($event->subscriber->course);

        // Send thanks for subscribing email.
        Mail::to($event->subscriber->email)->send(new SubscribedToCourseMail($event->subscriber));

        nova_notify($event->subscriber->course->admin, [
            'message' => 'New subscriber ('.$event->subscriber->email.')!',
            'icon' => 'plus-circle',
            'type' => 'success',
        ]);
    }
}
