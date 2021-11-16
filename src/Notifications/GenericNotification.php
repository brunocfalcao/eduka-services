<?php

namespace Eduka\Services\Notifications;

use Eduka\Abstracts\EdukaNotification;

/**
 * A generic notification is the most standard notification on Eduka.
 * Most of the email notifications will be sent using this class.
 *
 * The creation of the generic notification is as follows:
 * new GenericNotification(string $configPath, array $data = [])
 *
 * The $configPath is basically the config path for the course config file
 * that will point to a Mailable notification class.
 *
 * The $data is additional data we want to pass to the notification.
 *
 * By default, the notification will have the following data:
 *
 * $notifiable -> The target model class (normally a User model).
 * $data -> The additional data structure passed to the notification.
 * $url -> The course url. Used for instance to generate links to the course.
 * $course -> The course model instance (if contextualized).
 *
 * The generic notification will automatically contextualize the course
 * of which the generic notification was created, so it's important that
 * you DON'T call generic notifications from non-contextualize environments
 * like command/console calls.
 */
class GenericNotification extends EdukaNotification
{
    //
}
