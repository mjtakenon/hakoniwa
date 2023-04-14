<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByEarthquakeLog;
use App\Services\Hakoniwa\Log\DestructionByTyphoonLog;
use App\Services\Hakoniwa\Log\OccurEarthquakeLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurTyphoonLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;

class Typhoon implements IDisaster
{
//    private const OCCUR_PROBABILITY = 0.02;
    private const OCCUR_PROBABILITY = 0.5;
    private const DESTRUCTION_PROBABILITIES_PER_PREVENT_CELLS = [
        0 => 0.5,
        1 => 0.3,
        2 => 0.2,
        3 => 0.1,
        4 => 0,
        5 => 0,
        6 => 0,
    ];

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_TYPHOON];
        });

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            $aroundCells = $terrain->getAroundCells($cell->getPoint());
            $preventCells = $aroundCells->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellTypeConst::PREVENTING_TYPHOON];
            });

            if (self::DESTRUCTION_PROBABILITIES_PER_PREVENT_CELLS[$preventCells->count()] <= Rand::mt_rand_float()) {
                continue;
            }

            $terrain->setCell($cell->getPoint(), new Plain(point: $cell->getPoint()));
            $logs->add(new DestructionByTyphoonLog($island, $turn, $cell));
        }

        $logs->add(new OccurTyphoonLog($island, $turn));

        return new DisasterResult($terrain, $status, $logs);
    }
}
