<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoUplsertEvent;
use Eduka\Services\Jobs\Vimeo\UploadVideoJob as UploadVideoVimeo;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class TempFilenamePathChangedListener extends EdukaListener
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(VideoUplsertEvent $event)
    {
        $batch = Bus::batch([
            new UploadVideoVimeo($event->video),
            // new UplsertVideoJobYoutube($event->video),
            // new UplsertVideoJobBackblaze($event->video)
        ])->then(function (Batch $batch) use ($event) {
            // Delete physical file. No longer needed. No job needed.
            Storage::delete($event->video->temp_filename_path);

            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => 'Video uploaded to all platforms',
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
