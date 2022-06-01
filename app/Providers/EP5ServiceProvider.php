<?php

namespace App\Providers;

use App\Http\Resources\EP5\PlaybackCommandResource;
use App\Services\EP5Service;
use Illuminate\Support\ServiceProvider;

class EP5ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\EP5Service', function () {
            return new EP5Service();
        });

        $this->app->bind(
            'App\Repositories\EP5\AnnotationRepositoryInterface',
            'App\Repositories\EP5\AnnotationRepository'
        );

        $this->app->bind(
            'App\Repositories\EP5\PlaybackCommandRepositoryInterface',
            'App\Repositories\EP5\PlaybackCommandRepository'
        );

        $this->app->bind(
            'App\Repositories\EP5\VideoSequenceRepositoryInterface',
            'App\Repositories\EP5\VideoSequenceRepository'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        PlaybackCommandResource::withoutWrapping();
    }
}
