<?php

namespace App\Providers;

use App\Repositories\MessageMentioningsRepository;
use App\Repositories\MessageMentioningsRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UserRepository;
use App\Services\MessageService;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MessageMentioningsRepositoryInterface::class, MessageMentioningsRepository::class);
        $this->app->bind(MessageService::class, function () {
            return new MessageService(
                app(MessageMentioningsRepository::class),
                app(SectionRepository::class),
                app(ProjectRepository::class),
                app(UserRepository::class),
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
