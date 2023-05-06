<?php

namespace App\Providers;

use App\Services\YConnectClient\YConnectClientBuilderService;
use Illuminate\Support\ServiceProvider;

class YConnectClientBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('YConnectClientBuilderService', function ($app) {
            return new YConnectClientBuilderService($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
