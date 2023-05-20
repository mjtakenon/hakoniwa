<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\DestructionByFireLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class Fire implements IDisaster
{
    const OCCUR_PROBABILITY = 0.01;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        /** @var Cell $cell */
        $candidates = $terrain->findByAttribute(CellConst::DESTRUCTIBLE_BY_FIRE);

        foreach ($candidates as $cell) {

            if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }

            $aroundCells = $terrain->getAroundCells($cell->getPoint());

            $aroundCells = $aroundCells->filter(function ($c) {
                return $c::ATTRIBUTE[CellConst::PREVENTING_FIRE];
            });

            if ($aroundCells->count() >= 1) {
                continue;
            }

            $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            $logs->add(new DestructionByFireLog($island, $cell));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
