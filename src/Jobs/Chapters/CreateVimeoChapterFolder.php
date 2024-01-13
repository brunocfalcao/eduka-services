<?php

namespace Eduka\Services\Jobs\Chapters;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Cube\Models\Chapter;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateVimeoChapterFolder implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $chapterId;

    public function __construct(int $chapterId)
    {
        $this->chapterId = $chapterId;
    }

    public function handle()
    {
        $chapter = Chapter::firstWhere('id', $this->chapterId);
        $course = $chapter->course;

        $data = VimeoClient::upsertFolder(
            $chapter->name,
            $course->vimeo_uri
        );

        // Update the vimeo uri and vimeo folder id.
        $chapter->update([
            'vimeo_uri' => $data['body']['uri'],
            'vimeo_folder_id' => last(explode('/', $data['body']['uri'])),
        ]);
    }
}
