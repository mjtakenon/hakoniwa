<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\DestructionByHugeMeteoriteLog;
use App\Entity\Log\LogRow\DestructionShipLog;
use App\Entity\Log\LogRow\OccurHugeMeteoriteLog;
use App\Entity\Log\LogRow\ScatterAwayByHugeMeteoriteLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class HugeMeteorite implements IDisaster
{
    const OCCUR_PROBABILITY = 0.005;
    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $point = new Point(mt_rand(0, \HakoniwaService::getMaxWidth() - 1), mt_rand(0, \HakoniwaService::getMaxHeight() - 1));

        $around1HexCells = $terrain->getAroundCells($point);
        $around2HexCells = $terrain->getAroundCells($point, 2);

        $terrain->setCell($point, new Sea(point: $point));

        $logs->add(new OccurHugeMeteoriteLog($island, $point));

        // 周囲1hex
        /** @var Cell $cell */
        foreach ($around1HexCells as $cell) {
            $around2HexCells = $around2HexCells->reject(function ($c) use ($cell) {
                return $c->getPoint()->toString() === $cell->getPoint()->toString();
            });

            if ($cell::ATTRIBUTE[CellConst::IS_MONSTER]) {
                $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $cell));
            } else if ($cell::ATTRIBUTE[CellConst::IS_SHIP]) {
                $logs->add(new DestructionShipLog($island, $cell));
            } else {
                $logs->add(new DestructionByHugeMeteoriteLog($island, $cell, 1));
            }

            $terrain->setCell($cell->getPoint(), CellConst::getDefaultCell($cell->getPoint(), $cell->getElevation()-1));
        }

        // 周囲2hex
        foreach ($around2HexCells as $cell) {
            if (!$cell::ATTRIBUTE[CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX]) {
                continue;
            }

            if ($cell::ATTRIBUTE[CellConst::IS_MONSTER]) {
                $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $cell));
            } else if ($cell::ATTRIBUTE[CellConst::IS_SHIP]) {
                $logs->add(new DestructionShipLog($island, $cell));
            } else {
                $logs->add(new DestructionByHugeMeteoriteLog($island, $cell, 2));
            }

            $terrain->setCell($cell->getPoint(), CellConst::getDefaultCell($cell->getPoint(), $cell->getElevation()));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
