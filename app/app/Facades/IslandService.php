<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class IslandService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'IslandService';
    }
}
