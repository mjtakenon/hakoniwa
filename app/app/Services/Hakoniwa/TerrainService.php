<?php

namespace App\Services\Hakoniwa;

use Illuminate\Support\ServiceProvider;

class TerrainService extends ServiceProvider
{
    const MAX_HEIGHT = 15;
    const MAX_WIDTH = 15;

    public function initTerrain() {


        for ($x = 0; $x < self::MAX_WIDTH; $x++) {
            for ($y = 0; $y < self::MAX_HEIGHT; $y++) {

            }
        }
    }

    private function toJson() {
        return ;
    }
}
