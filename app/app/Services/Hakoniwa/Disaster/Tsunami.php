<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByTsunamiLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurTsunamiLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;

class Tsunami implements IDisaster
{
    private const OCCUR_PROBABILITY = 0.015;
    private const DESTRUCTION_PROBABILITIES_PER_SEA_CELLS = [
        6 => 0,
        5 => 0,
        4 => 0.08,
        3 => 0.16,
        2 => 0.24,
        1 => 0.32,
        0 => 0.40,
    ];

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI];
        });

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            $aroundCells = $terrain->getAroundCells($cell->getPoint());
            $preventCells = $aroundCells->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellTypeConst::PREVENTING_TSUNAMI];
            });

            if (self::DESTRUCTION_PROBABILITIES_PER_SEA_CELLS[$preventCells->count()] <= Rand::mt_rand_float()) {
                continue;
            }

            $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            $logs->add(new DestructionByTsunamiLog($island, $turn, $cell));
        }

        $logs->add(new OccurTsunamiLog($island, $turn));

        return new DisasterResult($terrain, $status, $logs);
    }
}
