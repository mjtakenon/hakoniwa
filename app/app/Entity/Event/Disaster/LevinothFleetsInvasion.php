<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\LevinothBattleship;
use App\Entity\Cell\Ship\LevinothSubmarine;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

class LevinothFleetsInvasion implements IDisaster
{
    private static function getInitialExperience(int $population): int
    {
        return (int)(($population / 80000) * 5);
    }

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        // 潜水艦
        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->isEmpty()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $maxEnemyShipsCount = (int)floor($status->getPopulation() / 160000);
        $maxEnemyShipsCount = min(5, $maxEnemyShipsCount);

        if ($maxEnemyShipsCount <= 0) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $spawnCells = $seaCells->random(min($maxEnemyShipsCount, $seaCells->count()));

        foreach ($spawnCells as $spawnCell) {
            $terrain->setCell(new LevinothSubmarine(
                point: $spawnCell->getPoint(),
                elevation: $spawnCell->getElevation(),
                experience: random_int(0, self::getInitialExperience($status->getPopulation())),
                affiliation_id: LevinothSubmarine::AFFILIATION_ENEMY,
                return_turn: $turn->turn + LevinothSubmarine::DEFAULT_RETURN_TURN,
            ));
        }

        // 戦艦
        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->isEmpty()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $spawnCells = $seaCells->random(min($maxEnemyShipsCount, $seaCells->count()));

        foreach ($spawnCells as $spawnCell) {
            $terrain->setCell(new LevinothBattleship(
                point: $spawnCell->getPoint(),
                elevation: $spawnCell->getElevation(),
                experience: random_int(0, self::getInitialExperience($status->getPopulation())),
                affiliation_id: LevinothBattleship::AFFILIATION_ENEMY,
                return_turn: $turn->turn + LevinothBattleship::DEFAULT_RETURN_TURN,
            ));
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
