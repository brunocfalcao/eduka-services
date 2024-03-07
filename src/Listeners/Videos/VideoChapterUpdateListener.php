<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoChapterUpdatedEvent;
use Eduka\Cube\Models\Chapter;
use Eduka\Services\Jobs\Vimeo\DeleteVideoFromFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class VideoChapterUpdateListerner extends EdukaListener
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(VideoChapterUpdatedEvent $event)
    {
        /**
         * We always remove the video from the current chapter folder (in case it's not null).
         * We just add the video to the new chapter folder in case it's not null.
         */
        $previousChapterModel = $event->previousChapterId ??
                                Chapter::find($event->previousChapterId);

        $jobs = [];

        if ($previousChapterModel) {
            // 2 models passed to the job: The Previous Chapter, and the current video.
            $jobs[] = new DeleteVideoFromFolderJob($event->previousChapterModel->vimeo_uri, $event->video->vimeo_uri);
        }

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($admin, $videoName) {
                // Notify the course admin.
                nova_notify($admin, [
                    'message' => 'Video "'.$videoName.'" deleted',
                    'icon' => 'video-camera',
                    'type' => 'info',
                ]);
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
