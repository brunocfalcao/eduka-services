<?php

namespace Eduka\Services\Listeners\Episodes;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Episodes\EpisodeChapterUpdatedEvent;
use Eduka\Cube\Models\Chapter;
use Eduka\Services\Jobs\Vimeo\AddEpisodeToFolderJob;
use Eduka\Services\Jobs\Vimeo\DeleteEpisodeFromFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class EpisodeChapterUpdateListener extends EdukaListener
{
    public function handle(EpisodeChapterUpdatedEvent $event)
    {
        $originalChapter = $event->episode->getOriginal('chapter_id') ?
                           Chapter::find($event->episode->getOriginal('chapter_id')) :
                           null;

        $jobs = [];

        if ($originalChapter) {
            // Delete episode from previous chapter.
            $jobs[] = new DeleteEpisodeFromFolderJob(
                $originalChapter->vimeo_uri,
                $event->episode->vimeo_uri
            );
        }

        $episodeAdded = false;

        // New episode chapter? Add it there.
        if ($event->episode->chapter_id) {
            $episodeAdded = true;

            $jobs[] = new AddEpisodeToFolderJob(
                $event->episode->chapter->vimeo_uri,
                $event->episode->vimeo_uri
            );
        }

        $admin = $event->episode->course->admin;
        $episodeName = $event->episode->name;

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($admin, $episodeName, $episodeAdded) {
                // Notify the course admin.
                nova_notify($admin, [
                    'message' => 'Episode "'.$episodeName.'" deleted from folder',
                    'icon' => 'dots-circle-horizontal',
                    'type' => 'success',
                ]);
                if ($episodeAdded) {
                    nova_notify($admin, [
                        'message' => 'Episode "'.$episodeName.'" added to new folder',
                        'icon' => 'episode-camera',
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
