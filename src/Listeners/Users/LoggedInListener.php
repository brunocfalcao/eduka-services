<?php

namespace Eduka\Services\Listeners\Users;

use Eduka\Abstracts\Classes\EdukaListener;
use Illuminate\Auth\Events\Login;

class LoggedInListener extends EdukaListener
{
    public function handle(Login $event)
    {
        $event->student->previous_logged_in_at = $event->student->last_logged_in_at;
        $event->student->last_logged_in_at = now();
        $event->student->save();
    }
}
