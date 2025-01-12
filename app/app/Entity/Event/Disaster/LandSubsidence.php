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

        $candidates = $terrain->getCells()->flatten(1)->filter(function (Cell $cell) {
            // 船や山ではない かつ 浅瀬以上の標高
            return !($cell::ATTRIBUTE[CellConst::IS_SHIP] || $cell::ATTRIBUTE[CellConst::IS_MOUNTAIN]) && $cell::ELEVATION >= CellConst::ELEVATION_SHALLOW;
        });

        /** @var Cell $cell */
        foreach ($candidates as $cell) {

            if ($cell::ELEVATION === CellConst::ELEVATION_SHALLOW) {
                $logs->add(new DestructionByLandSubsidenceLog($island, $cell));
                // FIXME 標高に合わせてセルを変えるよう実装
                $terrain->setCell(new Sea(point: $cell->getPoint(), elevation: $cell->getElevation()-1));
                continue;
            }

            if (self::DESTRUCTION_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }

            $logs->add(new DestructionByLandSubsidenceLog($island, $cell));
            // FIXME 標高に合わせてセルを変えるよう実装
            $terrain->setCell(new Shallow(point: $cell->getPoint(), elevation: $cell->getElevation()-1));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
