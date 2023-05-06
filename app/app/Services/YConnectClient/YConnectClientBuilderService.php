<?php

namespace App\Services\YConnectClient;

use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\ServiceProvider;
use YConnect\Credential\ClientCredential;
use YConnect\YConnectClient;

class YConnectClientBuilderService extends ServiceProvider
{
    public function build(): YConnectClient
    {
        $cred = new ClientCredential(
            config('services.yahoo.client_id'),
            config('services.yahoo.client_secret')
        );
        return new YConnectClient($cred);
    }
}
