<?php

namespace App\Services\Hakoniwa;

use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\ServiceProvider;

class HakoniwaService extends ServiceProvider
{
    public function isIslandRegisterd() {
        if (!\Auth::check()) {
            return false;
        }
        return !is_null(\Auth::user()->island);
    }


    /**
     * @return Turn
     */
    public function getLatestTurn() {
        return Turn::orderBy('created_at')->firstOrFail();
    }

    /**
     * @return Island
     */
    public function getOwnedIsland() {
        return \Auth::user()->island;
    }
}
