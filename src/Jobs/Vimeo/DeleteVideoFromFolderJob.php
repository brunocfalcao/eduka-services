<?php

namespace Eduka\Services\Jobs\Vimeo;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteVideoFromFolderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chapterURI;

    public $videoURI;

    public function __construct(?string $chapterURI, ?string $videoURI)
    {
        $this->chapterURI = $chapterURI;
        $this->videoURI = $videoURI;
    }

    public function handle()
    {
        dd('here');

        // Grab the vimeo_folder_id for the change.
        $data = VimeoClient::deleteVideoFromFolder(
            // The chapter vimeo_uri.
            $this->chapterURI,
            // The video vimeo_uri.
            $this->videoURI
        );
    }
}
