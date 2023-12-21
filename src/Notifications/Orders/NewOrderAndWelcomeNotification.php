<?php

namespace Eduka\Services\Notifications\Orders;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Cube\Models\Order;
use Eduka\Cube\Models\User;
use Eduka\Services\Mail\Orders\OrderCompletedAndWelcomeMail;

class NewOrderAndWelcomeNotification extends EdukaNotification
{
    public User $user;

    public Order $order;

    public string $url;

    public function __construct(User $user, Order $order, string $url)
    {
        $this->order = $order;
        $this->user = $user;
        $this->url = $url;
    }

    public function toMail($notifiable)
    {
        return (new OrderCompletedAndWelcomeMail($this->user, $this->order, $this->url))
                ->to($this->user->email);
    }
}
