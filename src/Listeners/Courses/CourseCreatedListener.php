<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Courses\CourseCreatedEvent;
use Eduka\Services\Jobs\Common\UpsertVimeoFolder;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

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
            new UpsertVimeoFolder($event->course),
        ])->then(function (Batch $batch) {
            // Send notification to course admin.
        })->dispatch();
    }
}
