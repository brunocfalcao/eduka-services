<?php

namespace Eduka\Services\Notifications\Courses;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Services\Mail\Courses\CourseSaved as CourseSavedMail;

class CourseSaved extends EdukaNotification
{
    public function toMail($notifiable)
    {
        return (new CourseSavedMail($notifiable))
                ->to(eduka_mail_to());
    }
}
