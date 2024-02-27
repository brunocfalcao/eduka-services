<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoReplacedEvent;
use Eduka\Services\Jobs\Vimeo\UpdateVideoJob;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class VideoUpdateListener extends EdukaListener
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(VideoReplacedEvent $event)
    {
        $batch = Bus::batch([
            new UpdateVideoJob($event->video),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => 'Video "'.$event->video->name.'" metadata updated',
                'icon' => 'document-duplicate',
                'type' => 'info',
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
