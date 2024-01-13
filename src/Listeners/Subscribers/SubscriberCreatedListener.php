<?php

namespace Eduka\Services\Listeners\Subscribers;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Subscribers\SubscriberCreatedEvent;
use Eduka\Services\Notifications\Subscribers\SubscriberCreatedNotification;

class SubscriberCreatedListener extends EdukaListener
{
    public function handle(SubscriberCreatedEvent $event)
    {
        // Send an appreciation notification for the course interest.
        $event->subscriber->notify(new SubscriberCreatedNotification($event->subscriber));
    }
}
