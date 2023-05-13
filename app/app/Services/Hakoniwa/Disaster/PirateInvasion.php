<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\Pirate;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByEarthquakeLog;
use App\Services\Hakoniwa\Log\OccurEarthquakeLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\PirateInvasionLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;
use Illuminate\Support\Collection;

class PirateInvasion implements IDisaster
{
    private const OCCUR_PROBABILITY = 0.01;

    private static function getInitialExperience(int $population): int
    {
        return (int)(($population/80000) * 5);
    }
    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $seaCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
        });

        $maxPiratesCount = (int)floor($status->getPopulation() / 80000);
        $maxPiratesCount = min(5, $maxPiratesCount);

        if ($maxPiratesCount <= 0) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $maxPiratesCount = min($seaCells->count(), $maxPiratesCount);

        /** @var Cell|Collection $pirateSpawnCells */
        $pirateSpawnCells = $seaCells->random($maxPiratesCount);
        foreach($pirateSpawnCells as $pirateSpawnCell) {
            $terrain->setCell($pirateSpawnCell->getPoint(), new Pirate(
                point: $pirateSpawnCell->getPoint(),
                elevation: $pirateSpawnCell->getElevation(),
                experience: random_int(0, self::getInitialExperience($status->getPopulation())),
                affiliation_id: Pirate::AFFILIATION_PIRATE,
                return_turn: $turn->turn + Pirate::DEFAULT_RETURN_TURN,
            ));
        }

        /** @var Cell $cell */
        $logs->add(new PirateInvasionLog($island, $turn));

        return new DisasterResult($terrain, $status, $logs);
    }
}
