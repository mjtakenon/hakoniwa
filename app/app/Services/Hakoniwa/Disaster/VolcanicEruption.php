<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByVolcanicEruptionLog;
use App\Services\Hakoniwa\Log\DestructionShipLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurVolcanicEruptionLog;
use App\Services\Hakoniwa\Log\ScatterAwayByVolcanicEruptionLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use App\Services\Hakoniwa\Util\Rand;
use function Sodium\add;

class VolcanicEruption implements IDisaster
{
    const OCCUR_PROBABILITY = 0.015;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $point = new Point(mt_rand(0, \HakoniwaService::getMaxWidth()-1),mt_rand(0, \HakoniwaService::getMaxHeight()-1));

        $terrain->setCell($point, new Mountain(point: $point));

        $aroundCells = $terrain->getAroundCells($point);
        /** @var Cell $cell */
        foreach ($aroundCells as $cell) {
            if ($cell::ELEVATION === -2) {
                if ($cell::ATTRIBUTE[CellTypeConst::IS_SHIP]) {
                    $logs->add(new DestructionShipLog($island, $turn, $cell));
                } else {
                    $logs->add(new DestructionByVolcanicEruptionLog($island, $turn, $cell));
                }
                $terrain->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
            } else {
                if ($cell::ATTRIBUTE[CellTypeConst::IS_MONSTER]) {
                    $logs->add(new ScatterAwayByVolcanicEruptionLog($island, $turn, $cell));
                } else if ($cell::ATTRIBUTE[CellTypeConst::IS_SHIP]) {
                    $logs->add(new DestructionShipLog($island, $turn, $cell));
                } else {
                    $logs->add(new DestructionByVolcanicEruptionLog($island, $turn, $cell));
                }
                $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            }
        }

        $logs->add(new OccurVolcanicEruptionLog($island, $turn, $point));

        return new DisasterResult($terrain, $status, $logs);
    }
}
