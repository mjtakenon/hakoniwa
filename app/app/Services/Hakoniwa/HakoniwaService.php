<?php

namespace App\Services\Hakoniwa;

use Illuminate\Support\ServiceProvider;

class HakoniwaService extends ServiceProvider
{
    public function isRegisterd() {
        if (!\Auth::check()) {
            return false;
        }
        return !is_null(\Auth::user()->island);
    }
}
