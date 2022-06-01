<?php

namespace App\Providers;

use App\Repositories\MediaRepository;
use App\Repositories\MediaRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
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
