<?php

namespace Eduka\Services\Listeners\Chapters;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Chapters\ChapterRenamedEvent;
use Eduka\Services\Jobs\Common\UpsertVimeoFolder;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ChapterRenamedListener extends EdukaListener
{
    public function handle(ChapterRenamedEvent $event)
    {
        $batch = Bus::batch([
            new UpsertVimeoFolder($event->chapter, null, $event->chapter->vimeo_folder_id),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->dispatch();
    }
}
