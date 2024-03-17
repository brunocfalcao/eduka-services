<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoUpdatedEvent;
use Eduka\Services\Jobs\Vimeo\UpdateVideoJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class VideoUpdateListener extends EdukaListener
{
    public function handle(VideoUpdatedEvent $event)
    {
        $batch = Bus::batch([
            new UpdateVideoJob($event->video),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => 'Video "'.$event->video->name.'" metadata updated',
                'icon' => 'dots-circle-horizontal',
                'type' => 'success',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
