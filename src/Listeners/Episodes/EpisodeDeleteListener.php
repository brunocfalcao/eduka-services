<?php

namespace Eduka\Services\Listeners\Episodes;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Episodes\EpisodeDeletedEvent;
use Eduka\Services\Jobs\Vimeo\DeleteEpisodeJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class EpisodeDeleteListener extends EdukaListener
{
    public function handle(EpisodeDeletedEvent $event)
    {
        $admin = $event->payload['admin'];
        $episodeName = $event->payload['name'];
        $vimeoEpisodeURI = $event->payload['vimeo_uri'];

        $batch = Bus::batch([
            new DeleteEpisodeJob($event->payload['vimeo_uri']),
        ])->then(function (Batch $batch) use ($admin, $episodeName) {
            // Notify the course admin.
            nova_notify($admin, [
                'message' => 'Episode "'.$episodeName.'" deleted',
                'icon' => 'minus-circle',
                'type' => 'success',
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
