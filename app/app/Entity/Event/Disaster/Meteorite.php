<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\DestructionByMeteoriteLog;
use App\Entity\Log\LogRow\DestructionShipLog;
use App\Entity\Log\LogRow\OccurMeteoriteLog;
use App\Entity\Log\LogRow\ScatterAwayByHugeMeteoriteLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class Meteorite implements IDisaster
{
    const OCCUR_PROBABILITY = 0.015;
    const OCCUR_CONTINUOUSLY_PROBABILITY = 0.5;
    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $logs->add(new OccurMeteoriteLog($island));

        do {
            $point = new Point(mt_rand(0, \HakoniwaService::getMaxWidth()-1),mt_rand(0, \HakoniwaService::getMaxHeight()-1));
            $cell = $terrain->getCell($point);
            if (!$cell::ATTRIBUTE[CellConst::DESTRUCTIBLE_BY_METEORITE]) {
                continue;
            }

            if ($cell::ATTRIBUTE[CellConst::IS_MONSTER]) {
                $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $cell));
            } else if ($cell::ATTRIBUTE[CellConst::IS_SHIP]) {
                $logs->add(new DestructionShipLog($island, $cell));
            } else {
                $logs->add(new DestructionByMeteoriteLog($island, $cell));
            }

            $terrain->setCell($cell->getPoint(), CellConst::getDefaultCell($cell->getPoint(), $cell->getElevation()-1));

        } while (self::OCCUR_CONTINUOUSLY_PROBABILITY >= Rand::mt_rand_float());

        return new DisasterResult($terrain, $status, $logs);
    }
}
