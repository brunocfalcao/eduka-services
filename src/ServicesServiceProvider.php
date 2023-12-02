<?php

namespace Eduka\Services;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Services\Listeners\Subscribers\NewSubscription;
use Illuminate\Support\Facades\Event;

class ServicesServiceProvider extends EdukaServiceProvider
{
    public function boot()
    {
        $this->dir = __DIR__;

        $this->registerEvents();
        $this->registerViews();
        $this->registerCommands();

        parent::boot();
    }

    public function register()
    {
        //
    }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'eduka-services');
    }

    protected function registerEvents()
    {
        Event::listen(
            SubscriberCreated::class,
            [NewSubscription::class, 'handle']
        );
    }
}
