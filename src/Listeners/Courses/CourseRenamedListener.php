<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Courses\CourseRenamedEvent;
use Eduka\Services\Jobs\Vimeo\UpsertFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CourseRenamedListener extends EdukaListener
{
    public function handle(CourseRenamedEvent $event)
    {
        $batch = Bus::batch([
            new UpsertFolderJob($event->course, null, $event->course->vimeo_folder_id),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->admin, [
                'message' => 'Vimeo course folder renamed ('.$event->course->name.')',
                'icon' => 'dots-circle-horizontal',
                'type' => 'success',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
