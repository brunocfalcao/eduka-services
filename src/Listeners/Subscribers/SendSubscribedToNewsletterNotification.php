<?php

namespace Eduka\Services\Listeners\Subscribers;

use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Services\Notifications\Subscribers\SubscribedToNewsletter;

class SendSubscribedToNewsletterNotification
{
    public function handle(SubscriberCreated $event)
    {
        /**
         * Send email notification about the domain that was saved (created/updated).
         * In case we have a course_id, then we also mention the course data.
         */
        $event->subscriber->notify(new SubscribedToNewsletter($event->subscriber, $event->course));
    }
}
