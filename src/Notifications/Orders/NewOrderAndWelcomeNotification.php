<?php

namespace Eduka\Services\Notifications\Orders;

use Eduka\Abstracts\Classes\EdukaNotification;
use Eduka\Services\Mail\Subscribers\OrderCompletedAndWelcomeMail;

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
