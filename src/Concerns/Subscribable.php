<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Course;
use Eduka\Services\Notifications\ThankYouForSubscribing;

/**
 * Trait for the subscriber operations:
 * - Subscribe
 *   -> Updates the subscriber data, plus, if possible, the respective course.
 *   -> Sends notification to subscriber.
 *
 * - Unsubscribe
 *   -> Updates the subscriber data for the specific course
 */
trait Subscribable
{
    /**
     * Triggers a new subscription.
     * - Updates subscriber data.
     * - Sends email to the subscriber.
     *
     * @param  Course|null $course
     * @return void
     */
    public function subscribe(Course $course = null)
    {
        $course = $course ?? course();

        if ($course) {
            $this->course()->associate($course)->save();

            // Send notification to subscriber.
            $this->notify(new ThankYouForSubscribing('mail.subscribed'));
        }
    }

    public function unsubscribe()
    {
        $this->can_receive_emails = false;
        $this->save();
    }
}
