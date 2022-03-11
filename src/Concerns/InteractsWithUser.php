<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Course;

trait InteractsWithUser
{
    public function addCourse(Course $course, bool $asAdmin = false, bool $notify = false)
    {
        $this->courses()->attach([
            $course->id => ['is_admin' => $asAdmin]
        ]);
    }
}
