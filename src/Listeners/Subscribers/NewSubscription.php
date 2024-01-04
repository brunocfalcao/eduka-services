<?php

namespace Eduka\Services\Listeners\Subscribers;

use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Services\Notifications\Subscribers\SubscribedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewSubscription implements ShouldQueue
{
    public function handle(SubscriberCreated $event)
    {
        // Send an appreciation notification for the course interest.
        $event->subscriber->notify(new SubscribedNotification($event->subscriber));
    }
}
