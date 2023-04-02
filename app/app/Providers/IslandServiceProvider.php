<?php

namespace App\Providers;

use App\Services\Hakoniwa\IslandService;
use Illuminate\Support\ServiceProvider;

class IslandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('IslandService', function () {
            return new IslandService($this->app);
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
