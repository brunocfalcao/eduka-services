<?php

namespace Eduka\Services\Listeners\Subscribers;

use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Services\Notifications\Subscribers\Subscribed;

class NewSubscription
{
    public function handle(SubscriberCreated $event)
    {
        // Notify subscriber, to thank for the course interest.
        $event->subscriber->notify(new Subscribed($event->subscriber));
    }
}
