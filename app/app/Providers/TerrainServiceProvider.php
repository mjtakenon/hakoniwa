<?php

namespace App\Providers;

use App\Services\Hakoniwa\TerrainService;
use Illuminate\Support\ServiceProvider;

class TerrainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TerrainService', function () {
            return new TerrainService($this->app);
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
