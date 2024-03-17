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

class UploadEpisodeJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Episode $video)
    {
        $this->video = $video;
    }

    public function handle(): void
    {
        /**
         * Uploads a video to Vimeo, and have as destination the chapter folder
         * or the course root folder (in case there is no video chapter).
         */
        $uri = VimeoClient::uploadEpisode(
            storage_path('app/'.$this->video->temp_filename_path),
            $this->video->getVimeoEpisodeDefaultMetadata([
                'folder_uri' => $this->video->getUploadVimeoFolderURI(),
            ])
        );

        // Was there a previous video? If so, delete it. Dispatch asynch.
        if ($this->video->vimeo_uri) {
            DeleteEpisodeJob::dispatch($this->video->vimeo_uri);
        }

        // Update the video vimeo uri to the new uri.
        $this->video->update([
            'vimeo_uri' => $uri,
        ]);
    }
}
