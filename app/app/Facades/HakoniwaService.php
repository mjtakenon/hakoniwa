<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class HakoniwaService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'HakoniwaService';
    }
}
