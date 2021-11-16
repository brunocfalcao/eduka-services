<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Course;
use Eduka\Services\Notifications\GenericNotification;

trait RelatableToCourse
{
    /**
     * Adds a new user to a course.
     *
     * @param Course  $course
     * @param bool $isAdmin
     * @param bool $sendMail
     *
     * @return void
     */
    public function addToCourse(Course $course, bool $isAdmin = false, bool $sendMail = false)
    {
        if (! $this->courses->contains($this)) {
            // Add this model instance to the respective course instance.
            $this->courses()->syncWithPivotValues([
                $course->id,
            ], [
                'is_admin' => $isAdmin,
            ]);

            if ($sendMail) {
            // Send notification to user.
                $this->notify(new GenericNotification(
                    'mail.course.added',
                    'Your email account was added to the '.$course->name.'!',
                    $course->id
                ));
            }
        }
    }
}
