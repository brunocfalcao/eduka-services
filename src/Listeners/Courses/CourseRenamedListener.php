<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Courses\CourseRenamedEvent;
use Eduka\Services\Jobs\Vimeo\UpsertFolder;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Event\Code\Throwable;

class CourseRenamedListener extends EdukaListener
{
    public function handle(CourseRenamedEvent $event)
    {
        $batch = Bus::batch([
            new UpsertFolder($event->course, null, $event->course->vimeo_folder_id),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->adminUser, [
                'message' => '[ VIMEO ] - Course renamed ('.$event->course->name.')',
                'icon' => 'academic-cap',
                'type' => 'info',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->adminUser, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
