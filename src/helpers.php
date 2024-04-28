<?php

use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\View;

function register_course_view_namespace(Course $course)
{
    try {
        // Create a ReflectionClass object for the class
        $reflection = new ReflectionClass($course->provider_namespace);

        // Get the file name where the class is defined
        $filename = $reflection->getFileName();

        // Replace all '\' to '/', get the directory path.
        $path = str_replace('\\', '/', dirname($filename));

        View::addNamespace('course', $path . '/../resources/views');
    } catch (ReflectionException $e) {
        // Handle the error appropriately if the class does not exist
        echo 'Error: ' . $e->getMessage();
    }
}

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
