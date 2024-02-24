<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Videos\VideoUplsertEvent;
use Eduka\Services\Jobs\Vimeo\UplsertVideoJob as UplsertVideoJobVimeo;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class TempFilenamePathChangedListener extends EdukaListener
{
    public function handle(VideoUplsertEvent $event)
    {
        $batch = Bus::batch([
            new UplsertVideoJobVimeo($event->video),
            // new UplsertVideoJobYoutube(),
            // new UplsertVideoJobBackblaze()
        ])->then(function (Batch $batch) {
            // Delete physical file. No longer needed. No job needed.
            Storage::delete($event->video->temp_filename_path);

            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => 'Video uploaded to all platforms',
                'icon' => 'document-duplicate',
                'type' => 'info',
            ]);
        })->catch(function (Batch $batch, Throwable $e) {
            // Notify the course admin.
            nova_notify($event->video->course->admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
