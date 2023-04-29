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
use App\Services\Hakoniwa\Log\DestructionByHugeMeteoriteLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurHugeMeteoriteLog;
use App\Services\Hakoniwa\Log\ScatterAwayByHugeMeteoriteLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use App\Services\Hakoniwa\Util\Rand;

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

        // 周囲1hex
        /** @var Cell $cell */
        foreach ($around1HexCells as $cell) {
            $around2HexCells = $around2HexCells->reject(function ($c) use ($cell) {
                return $c->getPoint()->toString() === $cell->getPoint()->toString();
            });

            if ($cell::ELEVATION === -1) {
                if (in_array(Monster::class, class_parents($cell), true)) {
                    $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $turn, $cell));
                } else {
                    $logs->add(new DestructionByHugeMeteoriteLog($island, $turn, $cell, 1));
                }
                $terrain->setCell($cell->getPoint(), new Sea(point: $cell->getPoint()));
                continue;
            }

            if ($cell::ELEVATION === 0 || $cell::ELEVATION === 1) {
                if (in_array(Monster::class, class_parents($cell), true)) {
                    $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $turn, $cell));
                } else {
                    $logs->add(new DestructionByHugeMeteoriteLog($island, $turn, $cell, 1));
                }
                $terrain->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
                continue;
            }
        }

        // 周囲2hex
        foreach ($around2HexCells as $cell) {
            if ($cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX]) {
                if (in_array(Monster::class, class_parents($cell), true)) {
                    $logs->add(new ScatterAwayByHugeMeteoriteLog($island, $turn, $cell));
                } else {
                    $logs->add(new DestructionByHugeMeteoriteLog($island, $turn, $cell, 2));
                }
                $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            }
        }

        $logs->add(new OccurHugeMeteoriteLog($island, $turn, $point));

        return new DisasterResult($terrain, $status, $logs);
    }
}
