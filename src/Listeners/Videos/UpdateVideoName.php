<?php

namespace Eduka\Services\Listeners\Videos;

use Eduka\Cube\Events\Videos\VideoNameChanged;
use Eduka\Services\Jobs\ChangeVideoNameJob;

class UpdateVideoName
{
    public function handle(VideoNameChanged $event)
    {
        ChangeVideoNameJob::dispatch($event->video->id);
    }
}
