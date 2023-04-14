<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByFireLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;

class Fire implements IDisaster
{
    const OCCUR_PROBABILITY = 0.01;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        /** @var Cell $cell */
        $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_FIRE];
        });

        foreach ($candidates as $cell) {

            if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }

            $aroundCells = $terrain->getAroundCells($cell->getPoint());

            $aroundCells = $aroundCells->filter(function ($c) {
                return $c::ATTRIBUTE[CellTypeConst::PREVENTING_FIRE];
            });

            if ($aroundCells->count() >= 1) {
                continue;
            }

            $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            $logs->add(new DestructionByFireLog($island, $turn, $cell));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
