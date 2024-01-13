<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Courses\CourseRenamedEvent;
use Eduka\Services\Jobs\Common\UpsertVimeoFolder;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class CourseRenamedListener extends EdukaListener
{
    public function handle(CourseRenamedEvent $event)
    {
        $batch = Bus::batch([
            new UpsertVimeoFolder($event->course, null, $event->course->vimeo_folder_id),
        ])->then(function (Batch $batch) {
            // Send notification to course admin.
        })->dispatch();
    }
}
