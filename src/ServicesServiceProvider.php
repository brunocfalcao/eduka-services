<?php

namespace Eduka\Services;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Events\Courses\CourseSaved;
use Eduka\Cube\Events\Domains\DomainSaved;
use Eduka\Services\Listeners\Courses\SendCourseSavedNotification;
use Eduka\Services\Listeners\Domains\SendDomainSavedNotification;
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'services');
    }

    protected function registerEvents()
    {
        /**
         * When a new domain is saved in the database. It is triggered inside
         * the domain observer saved() method.
         */
        Event::listen(
            DomainSaved::class,
            [SendDomainSavedNotification::class, 'handle']
        );

        /**
         * When a new course is saved in the database. It is triggered inside
         * the course observer saved() method.
         */
        Event::listen(
            CourseSaved::class,
            [SendCourseSavedNotification::class, 'handle']
        );
    }
}
