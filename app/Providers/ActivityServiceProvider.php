<?php

namespace App\Providers;

use App\Repositories\ActivityRepository;
use App\Repositories\ActivityRepositoryInterface;
use App\Services\ActivityService;
use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ActivityRepositoryInterface::class, ActivityRepository::class);
        $this->app->bind(ActivityService::class, function () {
            return new ActivityService(
                app(ActivityRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
