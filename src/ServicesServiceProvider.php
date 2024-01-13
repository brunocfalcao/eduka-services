<?php

namespace Eduka\Services;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Events\Chapters\ChapterCreatedEvent;
use Eduka\Cube\Events\Chapters\ChapterRenamedEvent;
use Eduka\Cube\Events\Courses\CourseCreatedEvent;
use Eduka\Cube\Events\Courses\CourseRenamedEvent;
use Eduka\Cube\Events\Orders\OrderCreatedEvent;
use Eduka\Cube\Events\Subscribers\SubscriberCreatedEvent;
use Eduka\Services\Commands\TestCommand;
use Eduka\Services\Listeners\Chapters\ChapterCreatedListener;
use Eduka\Services\Listeners\Chapters\ChapterRenamedListener;
use Eduka\Services\Listeners\Courses\CourseCreatedListener;
use Eduka\Services\Listeners\Courses\CourseRenamedListener;
use Eduka\Services\Listeners\Orders\OrderCreatedListener;
use Eduka\Services\Listeners\Subscribers\SubscriberCreatedListener;
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
            SubscriberCreatedEvent::class,
            [SubscriberCreatedListener::class, 'handle']
        );

        Event::listen(
            OrderCreatedEvent::class,
            [OrderCreatedListener::class, 'handle']
        );

        Event::listen(
            ChapterCreatedEvent::class,
            [ChapterCreatedListener::class, 'handle']
        );

        Event::listen(
            ChapterRenamedEvent::class,
            [ChapterRenamedListener::class, 'handle']
        );

        Event::listen(
            CourseCreatedEvent::class,
            [CourseCreatedListener::class, 'handle']
        );

        Event::listen(
            CourseRenamedEvent::class,
            [CourseRenamedListener::class, 'handle']
        );
    }

    protected function registerCommands()
    {
        $this->commands([
            TestCommand::class,
        ]);
    }
}
