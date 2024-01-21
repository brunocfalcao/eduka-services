<?php

namespace Eduka\Services\Listeners\Orders;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Orders\OrderCreatedEvent;
use Eduka\Cube\Models\User;
use Eduka\Services\Notifications\Orders\NewOrderAndWelcomeNotification;
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

        // Does the email exist? -- If not, create user.
        $user = User::where('email', $email)->firstOrCreate([
            'email' => $order->user_email,
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

            // Send email notification.
            $user->notify(new NewOrderAndWelcomeNotification($user, $order, $url));
        }
    }
}
