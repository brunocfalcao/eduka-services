<?php

use Eduka\Cube\Models\Course;

function eduka_mail_from(Course $course = null)
{
    return $course ? $course->admin_email :
                     config('eduka.mail.from.email');
}

function eduka_mail_name(Course $course = null)
{
    return $course ? $course->admin_from :
                     config('eduka.mail.from.name');
}

function eduka_mail_to(Course $course = null)
{
    return $course ? $course->admin_email :
                     config('eduka.mail.to');
}
