<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoReplacedEvent;
use Eduka\Services\Jobs\Vimeo\UploadVideoJob as UploadVideoVimeo;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class VideoUploadListener extends EdukaListener
{
    public function handle(VideoReplacedEvent $event)
    {
        $batch = Bus::batch([
            // Upload video to Vimeo.
            new UploadVideoVimeo($event->video),
        ])->then(function (Batch $batch) use ($event) {
            // Delete physical file. No longer needed. No job needed.
            Storage::delete($event->video->temp_filename_path);

            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => 'Video "'.$event->video->name.'" uploaded to Vimeo',
                'icon' => 'video-camera',
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
