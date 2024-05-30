<?php

namespace Eduka\Services\Listeners\Orders;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Orders\OrderCreatedEvent;
use Eduka\Cube\Models\Student;
use Eduka\Services\Mail\Orders\OrderCreatedForExistingStudentMail;
use Eduka\Services\Mail\Orders\OrderCreatedForNewStudentMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class OrderCreatedListener extends EdukaListener
{
    // We can't retry, if so, the user is created twice.
    public $tries = 1;

    public function handle(OrderCreatedEvent $event)
    {
        /**
         * Order is created. Someone bought your course!
         * 1. Verify if it's a new student or a recurring student.
         * 2. New student? --> Thank you and send password reset link.
         * 3. Existing student? --> Thank you.
         *
         * 4. Update the order record with the student id.
         */
        $order = $event->order;
        $email = $order->student_email;

        /**
         * The logic of finding a new student needs to be computed using
         * the backend id. We can have the same email, but different
         * backend ids (because the platform is multiple backend).
         */
        $backend = $event->order->variant->course->backend;

        // Does the email exists?
        $student = Student::where('email', $email)->firstOrCreate([
            'email' => $email,
        ]);

        // Update student for order, right away.
        $order->update([
            'student_id' => $student->id,
        ]);

        // Attach the user to the course.
        $student->courses()->attach($event->order->variant->course->id);

        if ($student->wasRecentlyCreated) {
            /**
             * User was created on this lifecycle. We need to send
             * a welcome email, and a password reset link.
             */

            // Update name and random password, for the first time.
            $student->update([
                'name' => $order->student_name,
                'password' => Str::random(20),
            ]);

            // Create a password reset token for the student.
            $token = Password::broker()->createToken($student);

            // Construct password reset url (using eduka route).
            $url = eduka_route(
                $order->course->backend->domain,
                'password.reset',
                [
                    'token' => $token,
                    'email' => urlencode($student->email),
                ]
            );

            // Send email to the new student.
            Mail::to($student)->send(new OrderCreatedForNewStudentMail($student, $order, $url));
        } else {
            /**
             * User already exists. We need to send a thank you for buying
             * email and a link to access the website (backend).
             */

            // Send email to the new student.
            Mail::to($student)->send(new OrderCreatedForExistingStudentMail($student, $order));
        }

        nova_notify($order->course->admin, [
            'message' => 'Order completed ('.$student->email.')',
            'icon' => 'plus-circle',
            'type' => 'success',
        ]);
    }
}
