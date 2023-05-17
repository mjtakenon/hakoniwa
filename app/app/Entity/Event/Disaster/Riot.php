<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Cell\Volcano;
use App\Entity\Cell\Wasteland;
use App\Entity\Log\DestructionByRiotLog;
use App\Entity\Log\Logs;
use App\Entity\Log\OccurFoodShortageLog;
use App\Entity\Log\OccurRiotLog;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class Riot implements IDisaster
{
    private const DESTRUCTION_PROBABILITY = 0.25;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if ($status->getFoods() > 0) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $logs->add(new OccurRiotLog($island));
        $logs->add(new OccurFoodShortageLog($island));

        $candidates = $terrain->findByAttribute(CellConst::DESTRUCTIBLE_BY_RIOT);

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            if (self::DESTRUCTION_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }

            if ($cell->getElevation() >= 1) {
                $terrain->setCell($cell->getPoint(), new Volcano(point: $cell->getPoint()));
            } else if ($cell->getElevation() === 0) {
                $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            } else if ($cell->getElevation() === -1) {
                $terrain->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
            } else {
                $terrain->setCell($cell->getPoint(), new Sea(point: $cell->getPoint()));
            }
            $logs->add(new DestructionByRiotLog($island, $cell));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
