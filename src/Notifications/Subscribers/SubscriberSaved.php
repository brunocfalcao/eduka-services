<?php

namespace Eduka\Services\Notifications\Domains;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Services\Mail\Subscribers\SubscribeToNewsletter;

class SubscriberSaved extends EdukaNotification
{
    public function toMail($notifiable)
    {
        return (new SubscribeToNewsletter($notifiable))
                ->to(config('eduka.mail.to.email'));
    }
}
