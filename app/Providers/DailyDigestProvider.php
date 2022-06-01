<?php

namespace App\Providers;

use App\Repositories\DailyDigestRepository;
use App\Repositories\DailyDigestRepositoryInterface;
use App\Services\DailyDigestService;
use Illuminate\Support\ServiceProvider;

class DailyDigestProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DailyDigestRepositoryInterface::class, DailyDigestRepository::class);
        $this->app->bind(DailyDigestService::class, function () {
            return new DailyDigestService(
                app(DailyDigestRepository::class)
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
