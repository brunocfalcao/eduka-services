<?php

namespace Eduka\Services\Listeners\Episodes;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Episodes\EpisodeReplacedEvent;
use Eduka\Services\Jobs\Vimeo\UploadEpisodeJob as UploadEpisodeVimeo;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class EpisodeUploadListener extends EdukaListener
{
    public function handle(EpisodeReplacedEvent $event)
    {
        info('triggering batch for episode upload...');
        $batch = Bus::batch([
            // Upload episode to Vimeo.
            new UploadEpisodeVimeo($event->episode),
        ])->then(function (Batch $batch) use ($event) {
            // Delete physical file. No longer needed. No job needed.
            Storage::delete($event->episode->temp_filename_path);

            // Notify the course admin.
            nova_notify($event->episode->course->admin, [
                'message' => 'Episode "'.$event->episode->name.'" uploaded to Vimeo',
                'icon' => 'cloud-upload',
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
