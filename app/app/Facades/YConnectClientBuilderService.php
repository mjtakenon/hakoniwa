<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class YConnectClientBuilderService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'YConnectClientBuilderService';
    }
}
