<?php

namespace App\Providers;

use App\Repositories\EP5\VideoSequenceRepository;
use App\Repositories\MediaRepository;
use App\Services\PlayListService;
use Illuminate\Support\ServiceProvider;

class PlayListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PlayListService::class, function () {
            return new PlayListService(
                app(VideoSequenceRepository::class),
                app(MediaRepository::class)
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
