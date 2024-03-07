<?php

namespace Eduka\Services\Jobs\Vimeo;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Cube\Models\Video;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadVideoJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(): void
    {
        /**
         * Uploads a video to Vimeo, and have as destination the chapter folder
         * or the course root folder (in case there is no video chapter).
         */
        $uri = VimeoClient::uploadVideo(
            storage_path('app/'.$this->video->temp_filename_path),
            $this->video->getVimeoVideoDefaultMetadata([
                'folder_uri' => $this->video->getUploadVimeoFolderURI(),
            ])
        );

        // Was there a previous video? If so, delete it. Dispatch asynch.
        if ($this->video->vimeo_uri) {
            DeleteVideoJob::dispatch($this->video->vimeo_uri);
        }

        // Update the video vimeo uri to the new uri.
        $this->video->update([
            'vimeo_uri' => $uri,
        ]);
    }
}
