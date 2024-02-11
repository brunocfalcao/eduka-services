<?php

namespace Eduka\Services\Jobs\Vimeo;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpsertFolderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;

    public $parentUri;

    public $folderId;

    public function __construct($model, $parentUri = null, $folderId = null)
    {
        $this->model = $model;
        $this->parentUri = $parentUri;
        $this->folderId = $folderId;
    }

    public function handle()
    {
        // Grab the vimeo_folder_id for the change.
        $data = VimeoClient::upsertFolder(
            $this->model->name,
            $this->parentUri,
            $this->folderId
        );

        $uri = $data['body']['uri'];

        // Update the vimeo uri and vimeo folder id.
        $this->model->update([
            'vimeo_uri' => $uri,
            'vimeo_folder_id' => last(explode('/', $uri)),
        ]);
    }
}
