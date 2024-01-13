<?php

namespace Eduka\Services\Jobs\Courses;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Cube\Models\Course;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateVimeoCourseFolder implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $courseId;

    public function __construct(int $courseId)
    {
        $this->courseId = $courseId;
    }

    public function handle()
    {
        $course = Course::firstWhere('id', $this->courseId);

        $data = VimeoClient::upsertFolder(
            $course->name
        );

        // Update the vimeo uri and vimeo folder id.
        $course->update([
            'vimeo_uri' => $data['body']['uri'],
            'vimeo_folder_id' => last(explode('/', $data['body']['uri'])),
        ]);
    }
}
