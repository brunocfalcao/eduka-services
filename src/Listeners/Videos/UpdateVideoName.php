<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Cube\Events\Videos\VideoNameChanged;
use Eduka\Services\Jobs\ChangeVideoNameJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateVideoName implements ShouldQueue
{
    public function handle(VideoNameChanged $event)
    {
        ChangeVideoNameJob::dispatch($event->video->id);
    }
}
