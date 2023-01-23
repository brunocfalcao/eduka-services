<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Cube\Events\Courses\CourseSaved as CourseSavedEvent;
use Eduka\Services\Notifications\Courses\CourseSaved as CourseSavedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseSavedNotification implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(CourseSavedEvent $event)
    {
        /**
         * Send email notification about the course that was saved (created/updated).
         * In case we have a course_id, then we also mention the course data.
         */
        $event->course->notify(new CourseSavedNotification());
    }
}
