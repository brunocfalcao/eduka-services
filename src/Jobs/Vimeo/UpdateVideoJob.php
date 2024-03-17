<?php

namespace Eduka\Services\Jobs\Vimeo;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Cube\Models\Episode;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateVideoJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Episode $video)
    {
        $this->video = $video;
    }

    public function handle(): void
    {
        VimeoClient::updateVideoDetails(
            $this->video->vimeo_uri,
            $this->video->getVimeoVideoefaultMetadata()
        );
    }
}
