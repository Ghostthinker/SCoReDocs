<?php

namespace App\Providers;

use App\Repositories\MediaRepository;
use App\Repositories\MediaRepositoryInterface;
use App\Services\UploadService;
use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(UploadService::class, function () {
            return new UploadService(app(MediaRepository::class));
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
