<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterDeletedEvent;
use Eduka\Services\Jobs\Vimeo\DeleteFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ChapterDeletedListener extends EdukaListener
{
    public function handle(ChapterDeletedEvent $event)
    {
        $admin = $event->chapter->course->admin;
        $chapterName = $event->chapter->name;
        $vimeoFolderId = $event->chapter->vimeo_folder_id;

        $batch = Bus::batch([
            new DeleteFolderJob($vimeoFolderId),
        ])->then(function (Batch $batch) use ($admin, $chapterName) {
            // Notify the course chapter admin.
            nova_notify($admin, [
                'message' => 'Vimeo chapter folder deleted ('.$chapterName.')',
                'icon' => 'folder-minus',
                'type' => 'info',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($admin) {
            info('[ Listener ] - Job went on error - '.$e->message());
            // Notify the course chapter admin.
            nova_notify($admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
