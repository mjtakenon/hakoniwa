<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Log\LogRow\DestructionByLandSubsidenceLog;
use App\Entity\Log\LogRow\OccurLandSubsidenceLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class LandSubsidence implements IDisaster
{
    private const LAND_SUBSIDENCE_OCCUR_THRESHOLD = 10000;
    private const OCCUR_PROBABILITY = 0.03;
    private const DESTRUCTION_PROBABILITY = 0.20;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if ($status->getArea() <= self::LAND_SUBSIDENCE_OCCUR_THRESHOLD) {
            return new DisasterResult($terrain, $status, $logs);
        }

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $logs->add(new OccurLandSubsidenceLog($island));

        $candidates = $terrain->getCells()->flatten(1)->filter(function ($cell) {
            return $cell::ELEVATION === CellConst::ELEVATION_SHALLOW || $cell::ELEVATION === CellConst::ELEVATION_PLAIN;
        });

        /** @var Cell $cell */
        foreach ($candidates as $cell) {

            if ($cell::ELEVATION === CellConst::ELEVATION_SHALLOW) {
                $logs->add(new DestructionByLandSubsidenceLog($island, $cell));
                $terrain->setCell($cell->getPoint(), new Sea(point: $cell->getPoint()));
                continue;
            }

            if (self::DESTRUCTION_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }

            $logs->add(new DestructionByLandSubsidenceLog($island, $cell));
            $terrain->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
