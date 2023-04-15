<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByEarthquakeLog;
use App\Services\Hakoniwa\Log\DestructionByRiotLog;
use App\Services\Hakoniwa\Log\DestructionByVolcanicEruptionLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurEarthquakeLog;
use App\Services\Hakoniwa\Log\OccurFoodShortageLog;
use App\Services\Hakoniwa\Log\OccurRiotLog;
use App\Services\Hakoniwa\Log\OccurVolcanicEruptionLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use App\Services\Hakoniwa\Util\Rand;

class Riot implements IDisaster
{
    private const DESTRUCTION_PROBABILITY = 0.25;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if ($status->getFoods() > 0) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_RIOT];
        });

        /** @var Cell $cell */
        foreach ($candidates as $cell) {
            if (self::DESTRUCTION_PROBABILITY <= Rand::mt_rand_float()) {
                continue;
            }
            $terrain->setCell($cell->getPoint(), new Wasteland(point: $cell->getPoint()));
            $logs->add(new DestructionByRiotLog($island, $turn, $cell));
        }

        $logs->add(new OccurRiotLog($island, $turn));
        $logs->add(new OccurFoodShortageLog($island, $turn));

        return new DisasterResult($terrain, $status, $logs);
    }
}
