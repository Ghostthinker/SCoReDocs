<?php

namespace App\Providers;

use App\Repositories\AuditRepository;
use App\Repositories\FileRepository;
use App\Repositories\LinkRepository;
use App\Repositories\LinkRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\SectionMediaRepository;
use App\Repositories\SectionMediaRepositoryInterface;
use App\Repositories\SectionRepository;
use App\Repositories\SectionRepositoryInterface;
use App\Repositories\SectionTrashRepository;
use App\Repositories\SectionTrashRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\ActivityService;
use App\Services\ImageService;
use App\Services\SectionService;
use Illuminate\Support\ServiceProvider;

class SectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->bind(SectionTrashRepositoryInterface::class, SectionTrashRepository::class);
        $this->app->bind(SectionMediaRepositoryInterface::class, SectionMediaRepository::class);
        $this->app->bind(LinkRepositoryInterface::class, LinkRepository::class);

        $this->app->bind(SectionService::class, function () {
            return new SectionService(
                app(SectionRepository::class),
                app(UserRepository::class),
                app(AuditRepository::class),
                app(ActivityService::class)
            );
        });

        $this->app->bind(ImageService::class, function () {
            return new ImageService(
                app(SectionRepository::class),
                app(SectionMediaRepository::class),
                app(FileRepository::class)
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
