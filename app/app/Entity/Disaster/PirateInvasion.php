<?php

namespace App\Entity\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Cell\Ship\Pirate;
use App\Entity\Log\Logs;
use App\Entity\Log\PirateInvasionLog;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
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

        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        $maxPiratesCount = (int)floor($status->getPopulation() / 80000);
        $maxPiratesCount = min(5, $maxPiratesCount);

        if ($maxPiratesCount <= 0) {
            return new DisasterResult($terrain, $status, $logs);
        }

        /** @var Cell $cell */
        $logs->add(new PirateInvasionLog($island));

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

        return new DisasterResult($terrain, $status, $logs);
    }
}
