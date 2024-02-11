<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterRenamedEvent;
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
            nova_notify($event->chapter->course->adminUser, [
                'message' => '[ VIMEO ] - Chapter renamed ('.$event->chapter->name.')',
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
