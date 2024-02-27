<?php

namespace Eduka\Services\Jobs\Vimeo;

use Eduka\Cube\Models\Video;
use Eduka\Services\External\Vimeo\VimeoClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(int $videoId)
    {
        $this->video = Video::firstWhere('id', $videoId);
    }

    public function handle(): void
    {
        // Update Vimeo video name.
        $vimeoClient = new VimeoClient();

        $vimeoClient->updateVideoDetails(
            $this->video->vimeo_id,
            $this->video->getVimeoVideoDefaultMetadata()
        );
    }
}
