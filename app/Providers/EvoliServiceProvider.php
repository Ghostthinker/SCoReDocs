<?php

namespace App\Providers;

use App\Services\EvoliService;
use Illuminate\Support\ServiceProvider;

class EvoliServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EvoliService::class, function () {
            return new EvoliService();
        });
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
