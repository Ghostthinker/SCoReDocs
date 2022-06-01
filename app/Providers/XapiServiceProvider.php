<?php

namespace App\Providers;

use App\Services\Xapi\XapiService;
use Illuminate\Support\ServiceProvider;

class XapiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Xapi', XapiService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
