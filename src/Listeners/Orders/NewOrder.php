<?php

namespace Eduka\Services\Listeners\Orders;

use Eduka\Cube\Events\Orders\OrderCreated;
use Eduka\Cube\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewOrder implements ShouldQueue
{
    public function handle(OrderCreated $event)
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
        $email = $order->email;

        // Does the email exist? -- If not, create user.
        $user = User::where('email', $email)->firstOrCreate([
            'email' => $order->user_email,
            'name' => $order->user_name,
            'password' => Str::random(20)
        ]);

        // Update user for order, right away.
        $order->update([
            'user_id' => $user->id
        ]);

        if ($user->wasRecentlyCreated) {
            /**
             * User was created on this lifecycle. We need to send
             * a welcome welcome email, and a password reset link.
             */
            // Create a token for the user
            $token = Password::broker()->createToken($user);

            var_dump($token);
            // Construct the password reset URL
            //$resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($user->email));
        }
    }
}
