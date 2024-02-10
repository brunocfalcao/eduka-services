<?php

namespace Eduka\Services\Listeners\Orders;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Orders\OrderCreatedEvent;
use Eduka\Cube\Models\User;
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
         * 1. Verify if it's a new user or a recurring user.
         * 2. New user? --> Thank you and send password reset link.
         * 3. Existing user? --> Thank you.
         *
         * 4. Update the order record with the user id.
         */
        $order = $event->order;
        $email = $order->user_email;

        /**
         * The logic of finding a new user needs to be computed using
         * the organization id. We can have the same email, but different
         * organization ids (because the platform is multiple organization).
         */
        $organization = $event->order->variant->course->organization;

        // Does the email exists within the same organization?
        $user = User::where('email', $email)->firstOrCreate([
            'email' => $email,
            'organization_id' => $organization->id,
        ]);

        $user->update([
            'name' => $order->user_name,
            'password' => Str::random(20),
        ]);

        // Update user for order, right away.
        $order->update([
            'user_id' => $user->id,
        ]);

        if ($user->wasRecentlyCreated) {
            /**
             * User was created on this lifecycle. We need to send
             * a welcome email, and a password reset link.
             */

            // Create a password reset token for the user.
            $token = Password::broker()->createToken($user);

            // Construct password reset url.
            $url = route(
                'password.reset',
                [
                    'token' => $token,
                    'email' => urlencode($user->email),
                ]
            );

            // Send email to the new user.
            Mail::to($user)->send(new OrderCreatedForNewUserMail($user, $order, $url));
        } else {
            /**
             * User already exists. We need to send a thank you for buying
             * email and a link to access the website (backend).
             */

            // Construct password reset url.
            $url = 'https://'.$order->course->organization->domain;

            // Send email to the new user.
            Mail::to($user)->send(new OrderCreatedForExistingUserMail($user, $order, $url));
        }
    }
}
