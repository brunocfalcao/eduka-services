<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterCreatedEvent;
use Eduka\Services\Jobs\Vimeo\UpsertFolder;
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
            new UpsertFolder(
                $event->chapter,
                $event->chapter->course->vimeo_uri
            ),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course chapter admin.
            nova_notify($event->chapter->course->adminUser, [
                'message' => '[ VIMEO ] - Chapter created ('.$event->chapter->name.')',
                'icon' => 'document-duplicate',
                'type' => 'info',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course chapter admin.
            nova_notify($event->chapter->course->adminUser, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}