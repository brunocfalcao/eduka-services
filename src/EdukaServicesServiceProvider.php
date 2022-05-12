<?php

namespace Eduka\Services;

use Illuminate\Support\ServiceProvider;

class EdukaServicesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        info('[EdukaServices][ServiceProvider] Start');
        info('[EdukaServices][ServiceProvider] Stop');
    }

    public function register()
    {
        //
    }
}
