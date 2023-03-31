<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TerrainService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'TerrainService';
    }
}
