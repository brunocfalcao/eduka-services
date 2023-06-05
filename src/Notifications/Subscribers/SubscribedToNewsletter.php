<?php

namespace Eduka\Services\Notifications\Subscribers;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Services\Mail\Subscribers\SubscribeToNewsletter;

class SubscribedToNewsletter extends EdukaNotification
{
    public function toMail($notifiable)
    {
        // @todo find a better name
        return (new SubscribeToNewsletter($notifiable))
                ->to(config('eduka.mail.to.email'));
    }
}
