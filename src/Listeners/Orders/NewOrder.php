<?php

namespace Eduka\Services\Listeners\Orders;

use Eduka\Cube\Events\Courses\OrderCreated;
use Illuminate\Tests\Integration\Queue\shouldQueue;

class NewOrder extends shouldQueue
{
    public function handle(OrderCreated $event)
    {
        /**
         * Check if LS variant exists.
         * Check if course exists for that variant.
         * Check if user exists, or create it.
         * Activate variant for user.
         * Send email notification (for password reset, or not).
         */
    }
}
