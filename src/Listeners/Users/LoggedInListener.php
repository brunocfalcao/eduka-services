<?php

namespace Eduka\Services\Listeners\Users;

use Eduka\Abstracts\Classes\EdukaListener;
use Illuminate\Auth\Events\Login;

class LoggedInListener extends EdukaListener
{
    public function handle(Login $event)
    {
        $event->user->last_logged_in_at = now();
        $event->user->save();
    }
}
