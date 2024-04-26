<?php

use Eduka\Cube\Models\Course;

function push_course_filesystem_driver(Course $course)
{
    config([
        'filesystems.disks.course' => [
            'driver' => 'local',
            'root' => storage_path('app/public/'.$course->canonical.'/'),
            'url' => env('APP_URL').'/storage/'.$course->canonical.'/',
            'visibility' => 'public',
            'throw' => false,
        ],
    ]);
}

function eduka_mail_from(?Course $course)
{
    // Get the admin user for the contextualized course.
    return $course->admin->email;
}

function eduka_mail_name(?Course $course = null)
{
    // Get the admin user name for the contextualized course.
    return $course->admin->name;
}

function eduka_mail_to(?Course $course = null)
{
    // Get the admin user for the contextualized course.
    return $course->admin->email;
}
