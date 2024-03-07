<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoDeletedEvent;
use Eduka\Services\Jobs\Vimeo\DeleteVideoJob;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class VideoDeleteListener extends EdukaListener
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(VideoDeletedEvent $event)
    {
        $admin = $event->payload['admin'];
        $videoName = $event->payload['name'];
        $vimeoVideoURI = $event->payload['vimeo_uri'];

        $batch = Bus::batch([
            new DeleteVideoJob($event->payload['vimeo_uri']),
        ])->then(function (Batch $batch) use ($admin, $videoName) {
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
