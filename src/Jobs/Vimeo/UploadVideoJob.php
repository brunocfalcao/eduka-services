<?php

namespace Eduka\Services\Jobs\Vimeo;

use Exception;
use Eduka\Cube\Models\Video;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Laravel\Nova\Notifications\NovaNotification;
use Brunocfalcao\VimeoClient\Facades\VimeoClient;

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
        $data = VimeoClient::uploadVideo(
            storage_path('app/'.$this->video->temp_filename_path),
            $this->video->getVimeoMetadata([
                'folder_uri' => $this->video->getUploadVimeoFolderURI(),
            ])
        );

        info($data);
    }
}
