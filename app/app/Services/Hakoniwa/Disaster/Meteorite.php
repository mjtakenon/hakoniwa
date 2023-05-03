<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByMeteoriteLog;
use App\Services\Hakoniwa\Log\DestructionShipLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurMeteoriteLog;
use App\Services\Hakoniwa\Log\ScatterAwayByHugeMeteoriteLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use App\Services\Hakoniwa\Util\Rand;

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

        do {
            $point = new Point(mt_rand(0, \HakoniwaService::getMaxWidth()-1),mt_rand(0, \HakoniwaService::getMaxHeight()-1));
            $cell = $terrain->getCell($point);
            if (!$cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_METEORITE]) {
                continue;
            }

            if ($cell::ATTRIBUTE[CellTypeConst::IS_MONSTER]) {
                $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $turn, $cell));
            } else if ($cell::ATTRIBUTE[CellTypeConst::IS_SHIP]) {
                $logs->add(new DestructionShipLog($island, $turn, $cell));
            } else {
                $logs->add(new DestructionByMeteoriteLog($island, $turn, $cell));
            }

            if ($cell::ELEVATION === 1) {
                $terrain->setCell($point, new Wasteland(point: $point));
            } else if ($cell::ELEVATION === 0) {
                $terrain->setCell($point, new Shallow(point: $point));
            } else {
                $terrain->setCell($point, new Sea(point: $point));
            }

        } while (self::OCCUR_CONTINUOUSLY_PROBABILITY >= Rand::mt_rand_float());

        $logs->add(new OccurMeteoriteLog($island, $turn));

        return new DisasterResult($terrain, $status, $logs);
    }
}
