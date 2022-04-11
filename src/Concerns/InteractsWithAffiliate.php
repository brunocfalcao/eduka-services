<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Course;

trait InteractsWithAffiliate
{
    public function addCourse(Course $course, bool $notify = false)
    {
        $this->course()->associate($course)->save();
    }
}
