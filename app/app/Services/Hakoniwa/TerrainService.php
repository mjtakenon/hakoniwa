<?php

namespace App\Services\Hakoniwa;

use App\Services\Hakoniwa\Cell\Sea;
use Illuminate\Support\ServiceProvider;

class TerrainService extends ServiceProvider
{
    const MAX_HEIGHT = 15;
    const MAX_WIDTH = 15;

    public function initTerrain() {

        $terrain = array();

        for ($x = 0; $x < self::MAX_WIDTH; $x++) {
            $row = array();
            for ($y = 0; $y < self::MAX_HEIGHT; $y++) {
                $row[] = new Sea($x, $y);
            }
            $terrain[] = $row;
        }

        var_dump($terrain[0][0]);
    }

    private function toJson() {
        return ;
    }
}
