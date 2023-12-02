<?php

namespace Eduka\Services\Listeners\Subscribers;

use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Services\Notifications\Subscribers\SubscribedNotification;

class NewSubscription
{
    public function handle(SubscriberCreated $event)
    {
        // Send an appreciation notification for the course interest.
        $event->subscriber->notify(new SubscribedNotification($event->subscriber));
    }
}
