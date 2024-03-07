<?php

namespace Eduka\Services;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Events\Chapters\ChapterCreatedEvent;
use Eduka\Cube\Events\Chapters\ChapterDeletedEvent;
use Eduka\Cube\Events\Chapters\ChapterRenamedEvent;
use Eduka\Cube\Events\Courses\CourseCreatedEvent;
use Eduka\Cube\Events\Courses\CourseRenamedEvent;
use Eduka\Cube\Events\Orders\OrderCreatedEvent;
use Eduka\Cube\Events\Subscribers\SubscriberCreatedEvent;
use Eduka\Cube\Events\Variants\VariantSavedEvent;
use Eduka\Cube\Events\Videos\VideoChapterUpdatedEvent;
use Eduka\Cube\Events\Videos\VideoDeletedEvent;
use Eduka\Cube\Events\Videos\VideoRenamedEvent;
use Eduka\Cube\Events\Videos\VideoReplacedEvent;
use Eduka\Cube\Events\Videos\VideoUpdatedEvent;
use Eduka\Services\Commands\TestCommand;
use Eduka\Services\Listeners\Chapters\ChapterCreatedListener;
use Eduka\Services\Listeners\Chapters\ChapterDeletedListener;
use Eduka\Services\Listeners\Chapters\ChapterRenamedListener;
use Eduka\Services\Listeners\Courses\CourseCreatedListener;
use Eduka\Services\Listeners\Courses\CourseRenamedListener;
use Eduka\Services\Listeners\Orders\OrderCreatedListener;
use Eduka\Services\Listeners\Subscribers\SubscriberCreatedListener;
use Eduka\Services\Listeners\Users\LoggedInListener;
use Eduka\Services\Listeners\Variants\VariantSavedListener;
use Eduka\Services\Listeners\Videos\VideoChapterUpdateListerner;
use Eduka\Services\Listeners\Videos\VideoDeleteListener;
use Eduka\Services\Listeners\Videos\VideoUpdateListener;
use Eduka\Services\Listeners\Videos\VideoUploadListener;
use Illuminate\Auth\Events\Login;
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
        /**
         * Events are registered conditionally, meaning we look at the
         * config file to know what events can be registered.
         */
        if (config('eduka.events.observers.video') === true) {
            /*
            Event::listen(
                VideoRenamedEvent::class,
                [<...>::class, 'handle']
            );
            */

            Event::listen(
                VideoChapterUpdatedEvent::class,
                [VideoChapterUpdateListerner::class, 'handle']
            );

            Event::listen(
                VideoDeletedEvent::class,
                [VideoDeleteListener::class, 'handle']
            );

            Event::listen(
                VideoUpdatedEvent::class,
                [VideoUpdateListener::class, 'handle']
            );

            Event::listen(
                VideoReplacedEvent::class,
                [VideoUploadListener::class, 'handle']
            );
        }

        if (config('eduka.events.observers.subscriber') === true) {
            Event::listen(
                SubscriberCreatedEvent::class,
                [SubscriberCreatedListener::class, 'handle']
            );
        }

        if (config('eduka.events.observers.variant') === true) {
            Event::listen(
                VariantSavedEvent::class,
                [VariantSavedListener::class, 'handle']
            );
        }

        if (config('eduka.events.observers.order') === true) {
            Event::listen(
                OrderCreatedEvent::class,
                [OrderCreatedListener::class, 'handle']
            );
        }

        if (config('eduka.events.observers.chapter') === true) {
            Event::listen(
                ChapterCreatedEvent::class,
                [ChapterCreatedListener::class, 'handle']
            );

            Event::listen(
                ChapterRenamedEvent::class,
                [ChapterRenamedListener::class, 'handle']
            );

            Event::listen(
                ChapterDeletedEvent::class,
                [ChapterDeletedListener::class, 'handle']
            );
        }

        if (config('eduka.events.observers.course') === true) {
            Event::listen(
                CourseCreatedEvent::class,
                [CourseCreatedListener::class, 'handle']
            );

            Event::listen(
                CourseRenamedEvent::class,
                [CourseRenamedListener::class, 'handle']
            );
        }

        // Universal event to log when a user logs in.
        Event::listen(
            Login::class,
            [LoggedInListener::class, 'handle']
        );
    }

    protected function registerCommands()
    {
        $this->commands([
            TestCommand::class,
        ]);
    }
}
