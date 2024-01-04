<?php

namespace Eduka\Services\Listeners\Courses;

use Eduka\Cube\Events\Courses\CourseCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateVimeoCourseFolder implements ShouldQueue
{
    public function handle(CourseCreated $event)
    {
        try {
            $course = Course::firstWhere('id', $this->courseId);
            $folder = VimeoClient::createFolder($course->name);
            $course->update(['vimeo_folder_uri' => $folder['body']['uri']]);
        } catch (Exception $e) {
            $message = 'Vimeo course folder creation error: '.$e->getMessage().' on file '.$e->getFile().' on line '.$e->getLine();
            User::firstWhere('id', $this->userId)->notify(
                NovaNotification::make()
                ->message($message)
                ->icon('download')
                ->type('error')
            );
        }
    }
}
