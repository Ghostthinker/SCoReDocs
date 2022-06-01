<?php

namespace App\Providers;

use App\Repositories\AuditRepository;
use App\Repositories\AuditRepositoryInterface;
use App\Repositories\MessageReadsRepository;
use App\Repositories\MessageReadsRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Repositories\MessageRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        \URL::forceScheme('https');
    }
}
