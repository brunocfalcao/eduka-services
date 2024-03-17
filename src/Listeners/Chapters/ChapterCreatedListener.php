<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterCreatedEvent;
use Eduka\Services\Jobs\Vimeo\UpsertFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ChapterCreatedListener extends EdukaListener
{
    public function handle(ChapterCreatedEvent $event)
    {
        $event->chapter->refresh();

        // Concurrency seeder check: course.vimeo_uri should be filled.
        if (blank($event->chapter->course->vimeo_uri)) {
            $this->release(5);

            return;
        }

        $batch = Bus::batch([
            new UpsertFolderJob(
                $event->chapter,
                $event->chapter->course->vimeo_uri
            ),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course chapter admin.
            nova_notify($event->chapter->course->admin, [
                'message' => 'Vimeo chapter folder created ('.$event->chapter->name.')',
                'icon' => 'folder-add',
                'type' => 'success',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course chapter admin.
            nova_notify($event->chapter->course->admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
