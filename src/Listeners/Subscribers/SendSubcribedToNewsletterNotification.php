<?php

namespace Eduka\Services\Listeners\Domains;

use Eduka\Cube\Events\Domains\SubscriberCreated;
use Eduka\Services\Notifications\Subscribers\SubscribedToNewsletter;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSubscribedToNewsletterNotification
{
    public function __construct()
    {
        //
    }

    public function handle(SubscriberCreated $event)
    {
        /**
         * Send email notification about the domain that was saved (created/updated).
         * In case we have a course_id, then we also mention the course data.
         */
        $event->subscriber->notify(new SubscribedToNewsletter());
    }
}
