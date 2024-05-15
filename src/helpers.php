<?php

use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\View;

/**
 * Mostly used to render full urls on newsletters and jobs that
 * will render views that have a course contextualized somehow.
 */
function eduka_url(string $domain, ?string $pathSuffix = null): string
{
    // Fetch the base URL components from the .env file, with sensible defaults
    $appUrl = env('APP_URL', 'http://localhost:8000');
    $urlParts = parse_url($appUrl);

    // Use the provided domain directly
    // Construct the base URL with scheme and provided domain
    $baseUrl = $urlParts['scheme'].'://'.$domain;

    // Check and append the port if available and if it's not a standard port
    if (isset($urlParts['port']) && ! in_array($urlParts['port'], [80, 443])) {
        $baseUrl .= ':'.$urlParts['port'];
    }

    // Finalize the URL construction
    $fullUrl = $baseUrl;

    // Append the path suffix if provided
    if (! is_null($pathSuffix)) {
        // Ensure there is exactly one '/' between segments
        $fullUrl .= '/'.ltrim($pathSuffix, '/');
    }

    return $fullUrl;
}

function register_course_view_namespace(Course $course)
{
    try {
        // Create a ReflectionClass object for the class
        $reflection = new ReflectionClass($course->provider_namespace);

        // Get the file name where the class is defined
        $filename = $reflection->getFileName();

        // Replace all '\' to '/', get the directory path.
        $path = str_replace('\\', '/', dirname($filename));

        View::addNamespace('course', $path.'/../resources/views');
    } catch (ReflectionException $e) {
        // Handle the error appropriately if the class does not exist
        echo 'Error: '.$e->getMessage();
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
