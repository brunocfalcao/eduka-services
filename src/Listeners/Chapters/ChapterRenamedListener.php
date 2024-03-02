<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterRenamedEvent;
use Eduka\Services\Jobs\Vimeo\UpsertFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ChapterRenamedListener extends EdukaListener
{
    public function handle(ChapterRenamedEvent $event)
    {
        $batch = Bus::batch([
            new UpsertFolderJob(
                $event->chapter,
                null,
                $event->chapter->vimeo_folder_id
            ),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course chapter admin.
            nova_notify($event->chapter->course->admin, [
                'message' => 'Vimeo chapter folder renamed ('.$event->chapter->name.')',
                'icon' => 'document-duplicate',
                'type' => 'info',
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
