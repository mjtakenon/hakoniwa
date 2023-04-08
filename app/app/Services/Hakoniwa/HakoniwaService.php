<?php

namespace App\Services\Hakoniwa;

use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\ServiceProvider;

class HakoniwaService extends ServiceProvider
{
    const MAX_HEIGHT = 15;
    const MAX_WIDTH = 15;

    public function isIslandRegistered() {
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
        if (!\Auth::check()) {
            return null;
        }
        return \Auth::user()->island;
    }

    public function getMaxWidth():int
    {
        return self::MAX_WIDTH;
    }

    public function getMaxHeight():int
    {
        return self::MAX_HEIGHT;
    }
}
