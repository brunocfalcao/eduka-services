<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterCreatedEvent;
use Eduka\Services\Jobs\Common\UpsertVimeoFolder;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ChapterCreatedListener extends EdukaListener
{
    public function handle(ChapterCreatedEvent $event)
    {
        /**
         * Batch the following jobs:
         * 1. Create Vimeo chapter folder name.
         * 4. Send notification to course admin + Nova.
         */
        $event->chapter->refresh();

        // Concurrency seeder check: course.vimeo_uri should be filled.
        if (blank($event->chapter->course->vimeo_uri)) {
            $this->release(5);

            return;
        }

        $batch = Bus::batch([
            new UpsertVimeoFolder(
                $event->chapter,
                $event->chapter->course->vimeo_uri
            ),
        ])->then(function (Batch $batch) {
            // Send notification to course admin.
        })->dispatch();
    }
}
