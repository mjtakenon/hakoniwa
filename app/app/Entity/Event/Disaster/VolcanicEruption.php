<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Mountain;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Volcano;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\ResourcesProduction\Mine;
use App\Entity\Log\LogRow\DestructionByVolcanicEruptionLog;
use App\Entity\Log\LogRow\DestructionShipLog;
use App\Entity\Log\LogRow\MineClosureLog;
use App\Entity\Log\LogRow\OccurVolcanicEruptionLog;
use App\Entity\Log\LogRow\ScatterAwayByVolcanicEruptionLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use function DeepCopy\deep_copy;

class VolcanicEruption implements IDisaster
{
    const OCCUR_PROBABILITY = 0.01;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        // 既存の火山と鉱山は休火山になる
        $mountains = $terrain->findByTypes([Volcano::TYPE, Mine::TYPE]);
        /** @var Cell $mountain */
        foreach ($mountains as $mountain) {
            $terrain->setCell($mountain->getPoint(), new Mountain(point: $mountain->getPoint()));
            if ($mountain->getType() === Mine::TYPE) {
                $logs->add(new MineClosureLog($island, deep_copy($mountain)));
            }
        }

        $point = new Point(mt_rand(0, \HakoniwaService::getMaxWidth() - 1), mt_rand(0, \HakoniwaService::getMaxHeight() - 1));

        $logs->add(new OccurVolcanicEruptionLog($island, $point));

        $terrain->setCell($point, new Volcano(point: $point));

        $aroundCells = $terrain->getAroundCells($point);
        /** @var Cell $cell */
        foreach ($aroundCells as $cell) {
            if ($cell::ATTRIBUTE[CellConst::IS_MONSTER]) {
                $logs->add(new ScatterAwayByVolcanicEruptionLog($island, $cell));
            } else if ($cell::ATTRIBUTE[CellConst::IS_SHIP]) {
                $logs->add(new DestructionShipLog($island, $cell));
            } else {
                $logs->add(new DestructionByVolcanicEruptionLog($island, $cell));
            }
            $terrain->setCell($cell->getPoint(), CellConst::getDefaultCell($cell->getPoint(), min(0, $cell->getElevation()+1)));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
