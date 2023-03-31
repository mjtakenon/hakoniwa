<?php

namespace App\Providers;

use App\Services\Hakoniwa\HakoniwaService;
use Illuminate\Support\ServiceProvider;

class HakoniwaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('HakoniwaService', function () {
            return new HakoniwaService($this->app);
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
