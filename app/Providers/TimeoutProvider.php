<?php

namespace App\Providers;

use App\Repositories\SectionRepository;
use App\Repositories\TimeoutRepository;
use App\Repositories\TimeoutRepositoryInterface;
use App\Services\SectionService;
use App\Services\TimeoutService;
use Illuminate\Support\ServiceProvider;

class TimeoutProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TimeoutRepositoryInterface::class, TimeoutRepository::class);

        $this->app->bind(TimeoutService::class, function () {
            return new TimeoutService(
                app(TimeoutRepository::class),
                app(SectionRepository::class),
                app(SectionService::class)
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
    }
}
