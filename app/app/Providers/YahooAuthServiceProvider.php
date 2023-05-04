<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use YConnect\Credential\ClientCredential;
use YConnect\YConnectClient;

class YahooAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(YConnectClient::class, function () {
            $cred = new ClientCredential(
                config('services.yahoo.client_id'),
                config('services.yahoo.client_secret')
            );
            return new YConnectClient($cred);
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
