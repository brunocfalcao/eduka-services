<?php

namespace Eduka\Services\Jobs\Vimeo;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteFolderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $vimeoFolderId;

    public function __construct(string $vimeoFolderId)
    {
        $this->vimeoFolderId = $vimeoFolderId;
    }

    public function handle()
    {
        // Grab the vimeo_folder_id for the change.
        $data = VimeoClient::deleteFolder(
            $this->vimeoFolderId,
        );
    }
}
