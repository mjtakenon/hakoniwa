<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\DestructionByTsunamiLog;
use App\Entity\Log\LogRow\OccurTsunamiLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

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

        $logs->add(new OccurTsunamiLog($island));

        $candidates = $terrain->findByAttribute(CellConst::DESTRUCTIBLE_BY_TSUNAMI);

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            $aroundCells = $terrain->getAroundCells($cell->getPoint(), 1, true);
            $preventCells = $aroundCells->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellConst::PREVENTING_TSUNAMI];
            });

            if (self::DESTRUCTION_PROBABILITIES_PER_SEA_CELLS[$preventCells->count()] <= Rand::mt_rand_float()) {
                continue;
            }

            $terrain->setCell($cell->getPoint(), CellConst::getDefaultCell($cell->getPoint(), $cell->getElevation()));
            $logs->add(new DestructionByTsunamiLog($island, $cell));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
