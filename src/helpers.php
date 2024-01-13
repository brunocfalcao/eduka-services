<?php

use Eduka\Cube\Models\Course;

function eduka_mail_from(?Course $course)
{
    // Get the admin user for the contextualized course.
    return $course->adminUser->email;
}

function eduka_mail_name(?Course $course = null)
{
    // Get the admin user name for the contextualized course.
    return $course->adminUser->name;
}

function eduka_mail_to(?Course $course = null)
{
    // Get the admin user for the contextualized course.
    return $course->adminUser->email;
}
