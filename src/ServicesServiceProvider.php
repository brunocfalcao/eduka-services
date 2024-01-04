<?php

namespace Eduka\Services;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Events\Orders\OrderCreated;
use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Cube\Events\Videos\VideoNameChanged;
use Eduka\Services\Commands\TestCommand;
use Eduka\Services\Listeners\Orders\NewOrder;
use Eduka\Services\Listeners\Subscribers\NewSubscription;
use Eduka\Services\Listeners\Videos\UpdateVideoName;
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

        Event::listen(
            OrderCreated::class,
            [NewOrder::class, 'handle']
        );

        Event::listen(
            VideoNameChanged::class,
            [UpdateVideoName::class, 'handle']
        );
    }

    protected function registerCommands()
    {
        $this->commands([
            TestCommand::class,
        ]);
    }
}
