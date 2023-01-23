<?php

namespace Eduka\Services\Listeners\Domains;

use Eduka\Cube\Events\Domains\DomainSaved as DomainSavedEvent;
use Eduka\Services\Notifications\Domains\DomainSaved as DomainSavedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDomainSavedNotification implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(DomainSavedEvent $event)
    {
        /**
         * Send email notification about the domain that was saved (created/updated).
         * In case we have a course_id, then we also mention the course data.
         */
        $event->domain->notify(new DomainSavedNotification());
    }
}
