<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Wasteland;
use App\Entity\Log\DestructionByEarthquakeLog;
use App\Entity\Log\Logs;
use App\Entity\Log\OccurEarthquakeLog;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class Earthquake implements IDisaster
{
    private const OCCUR_PROBABILITY = 0.005;
    private const DESTRUCTION_PROBABILITY = 0.25;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $logs->add(new OccurEarthquakeLog($island));

        $candidates = $terrain->findByAttribute(CellConst::DESTRUCTIBLE_BY_EARTHQUAKE);

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            if (self::DESTRUCTION_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }
            $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            $logs->add(new DestructionByEarthquakeLog($island, $cell));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
