<?php

namespace App\Providers;

use App\Repositories\MessageReadsRepository;
use App\Repositories\MessageReadsRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Services\MessageReadsService;
use Illuminate\Support\ServiceProvider;

class MessageReadsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MessageReadsRepositoryInterface::class, MessageReadsRepository::class);
        $this->app->bind(MessageReadsService::class, function () {
            return new MessageReadsService(
                app(MessageReadsRepository::class),
                app(MessageRepository::class)
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
