<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\DestructionByLandSubsidenceLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurLandSubsidenceLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;

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

        $logs->add(new OccurLandSubsidenceLog($island, $turn));

        $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ELEVATION === -1 || $cell::ELEVATION === 0;
        });

        /** @var Cell $cell */
        foreach ($candidates as $cell) {

            if ($cell::ELEVATION === -1) {
                $logs->add(new DestructionByLandSubsidenceLog($island, $turn, $cell));
                $terrain->setCell($cell->getPoint(), new Sea(point: $cell->getPoint()));
                continue;
            }

            if (self::DESTRUCTION_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }

            $logs->add(new DestructionByLandSubsidenceLog($island, $turn, $cell));
            $terrain->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
