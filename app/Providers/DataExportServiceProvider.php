<?php

namespace App\Providers;

use App\Repositories\DataExportRepository;
use App\Repositories\DataExportRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class DataExportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DataExportRepositoryInterface::class, DataExportRepository::class);
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
