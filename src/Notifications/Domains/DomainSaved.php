<?php

namespace Eduka\Services\Notifications\Domains;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Services\Mail\Domains\DomainSaved as DomainSavedMail;

class DomainSaved extends EdukaNotification
{
    public function toMail($notifiable)
    {
        return (new DomainSavedMail($notifiable))
                ->to(eduka_mail_to());
    }
}
