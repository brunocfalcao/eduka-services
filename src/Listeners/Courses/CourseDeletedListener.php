<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Courses\CourseDeletedEvent;
use Eduka\Services\Jobs\Vimeo\DeleteFolderJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CourseDeletedListener extends EdukaListener
{
    public function handle(CourseDeletedEvent $event)
    {
        $batch = Bus::batch([
            new DeleteFolderJob($event->course->vimeo_folder_id),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->admin, [
                'message' => 'Vimeo course folder deleted ('.$event->course->name.')',
                'icon' => 'minus-circle',
                'type' => 'warning',
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
