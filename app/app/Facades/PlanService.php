<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PlanService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'PlanService';
    }
}
