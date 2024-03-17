<?php

namespace Eduka\Services\Listeners\Episodes;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Episodes\EpisodeUpdatedEvent;
use Eduka\Services\Jobs\Vimeo\UpdateEpisodeJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class EpisodeUpdateListener extends EdukaListener
{
    public function handle(EpisodeUpdatedEvent $event)
    {
        $batch = Bus::batch([
            new UpdateEpisodeJob($event->episode),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->episode->course->admin, [
                'message' => 'Episode "'.$event->episode->name.'" metadata updated',
                'icon' => 'dots-circle-horizontal',
                'type' => 'success',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->episode->course->admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
