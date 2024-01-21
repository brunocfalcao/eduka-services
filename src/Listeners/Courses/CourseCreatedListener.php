<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Courses\CourseCreatedEvent;
use Eduka\Services\Jobs\Vimeo\UpsertFolder;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Event\Code\Throwable;

class CourseCreatedListener extends EdukaListener
{
    public function handle(CourseCreatedEvent $event)
    {
        /**
         * Batch the following jobs:
         * 1. Create a new Vimeo top-level folder.
         * 2. Create a new Backblaze bucket.
         * 3. Create a new YoutTube playlist.
         * 4. Send notification to course admin + Nova.
         */
        $batch = Bus::batch([
            new UpsertFolder($event->course),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->adminUser, [
                'message' => '[ VIMEO ] - Course created ('.$event->course->name.')',
                'icon' => 'academic-cap',
                'type' => 'info',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->adminUser, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}