<?php

namespace Eduka\Services\Jobs\Common;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Cube\Models\Chapter;
use Eduka\Cube\Models\Course;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenameVimeoFolder implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Chapter|Course $model;

    public function __construct(Chapter|Course $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        // Grab the vimeo_folder_id for the change.
        $data = VimeoClient::upsertFolder(
            $model->name,
            null,
            $model->folder_id
        );

        // Update the vimeo uri and vimeo folder id.
        $model->update([
            'vimeo_uri' => $data['body']['uri'],
            'vimeo_folder_id' => last(explode('/', $data['body']['uri'])),
        ]);
    }
}
