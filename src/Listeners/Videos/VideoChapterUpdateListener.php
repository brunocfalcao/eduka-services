<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoChapterUpdatedEvent;
use Eduka\Cube\Models\Chapter;
use Eduka\Services\Jobs\Vimeo\AddVideoToFolderJob;
use Eduka\Services\Jobs\Vimeo\DeleteVideoFromFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class VideoChapterUpdateListener extends EdukaListener
{
    public function handle(VideoChapterUpdatedEvent $event)
    {
        $originalChapter = $event->video->getOriginal('chapter_id') ?
                           Chapter::find($event->video->getOriginal('chapter_id')) :
                           null;

        $jobs = [];

        if ($originalChapter) {
            // Delete video from previous chapter.
            $jobs[] = new DeleteVideoFromFolderJob(
                $originalChapter->vimeo_uri,
                $event->video->vimeo_uri
            );
        }

        $videoAdded = false;

        // New video chapter? Add it there.
        if ($event->video->chapter_id) {
            $videoAdded = true;

            $jobs[] = new AddVideoToFolderJob(
                $event->video->chapter->vimeo_uri,
                $event->video->vimeo_uri
            );
        }

        $admin = $event->video->course->admin;
        $videoName = $event->video->name;

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($admin, $videoName, $videoAdded) {
                // Notify the course admin.
                nova_notify($admin, [
                    'message' => 'Video "'.$videoName.'" deleted from folder',
                    'icon' => 'dots-circle-horizontal',
                    'type' => 'success',
                ]);
                if ($videoAdded) {
                    nova_notify($admin, [
                        'message' => 'Video "'.$videoName.'" added to new folder',
                        'icon' => 'video-camera',
                        'type' => 'info',
                    ]);
                }
            })->catch(function (Batch $batch, Throwable $e) use ($admin) {
                // Notify the course admin.
                nova_notify($admin, [
                    'message' => $e->message(),
                    'icon' => 'exclamation-circle',
                    'type' => 'error',
                ]);
            })->dispatch();
    }
}
