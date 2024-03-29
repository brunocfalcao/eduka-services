<?php

namespace Eduka\Services\Listeners\Orders;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Orders\OrderCreatedEvent;
use Eduka\Cube\Models\Student;
use Eduka\Services\Mail\Orders\OrderCreatedForExistingUserMail;
use Eduka\Services\Mail\Orders\OrderCreatedForNewUserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class OrderCreatedListener extends EdukaListener
{
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

        // Does the email exists within the same backend?
        $student = User::where('email', $email)->firstOrCreate([
            'email' => $email,
            'backend_id' => $backend->id,
        ]);

        $student->update([
            'name' => $order->student_name,
            'password' => Str::random(20),
        ]);

        // Update student for order, right away.
        $order->update([
            'student_id' => $student->id,
        ]);

        if ($student->wasRecentlyCreated) {
            /**
             * User was created on this lifecycle. We need to send
             * a welcome email, and a password reset link.
             */

            // Create a password reset token for the student.
            $token = Password::broker()->createToken($student);

            // Construct password reset url.
            $url = route(
                'password.reset',
                [
                    'token' => $token,
                    'email' => urlencode($student->email),
                ]
            );

            // Send email to the new student.
            Mail::to($student)->send(new OrderCreatedForNewUserMail($student, $order, $url));
        } else {
            /**
             * User already exists. We need to send a thank you for buying
             * email and a link to access the website (backend).
             */

            // Construct password reset url.
            $url = 'https://'.$order->course->backend->domain;

            // Send email to the new student.
            Mail::to($student)->send(new OrderCreatedForExistingUserMail($student, $order, $url));
        }

        nova_notify($order->course->admin, [
            'message' => 'Order completed ('.$student->email.')',
            'icon' => 'plus-circle',
            'type' => 'success',
        ]);
    }
}
