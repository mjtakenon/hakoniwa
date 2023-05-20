<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Plain;
use App\Entity\Log\LogRow\DestructionByTyphoonLog;
use App\Entity\Log\LogRow\OccurTyphoonLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class Typhoon implements IDisaster
{
    private const OCCUR_PROBABILITY = 0.02;
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

        $logs->add(new OccurTyphoonLog($island));

        $candidates = $terrain->findByAttribute(CellConst::DESTRUCTIBLE_BY_TYPHOON);

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            $aroundCells = $terrain->getAroundCells($cell->getPoint());
            $preventCells = $aroundCells->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellConst::PREVENTING_TYPHOON];
            });

            if (self::DESTRUCTION_PROBABILITIES_PER_PREVENT_CELLS[$preventCells->count()] <= Rand::mt_rand_float()) {
                continue;
            }

            $terrain->setCell($cell->getPoint(), new Plain(point: $cell->getPoint()));
            $logs->add(new DestructionByTyphoonLog($island, $cell));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
