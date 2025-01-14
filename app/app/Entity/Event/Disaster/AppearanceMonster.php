<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\HasPopulation\City;
use App\Entity\Cell\HasPopulation\Metropolis;
use App\Entity\Cell\HasPopulation\Town;
use App\Entity\Cell\HasPopulation\Village;
use App\Entity\Cell\Monster\Monster;
use App\Entity\Cell\Monster\MonsterConst;
use App\Entity\Log\LogRow\AppearMonsterLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class AppearanceMonster implements IDisaster
{
    const OCCUR_PROBABILITY = 0.002;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        $appearableMonsters = MonsterConst::getAppearableMonsters($status->getPopulation());
        if ($appearableMonsters->count() <= 0) {
            return new DisasterResult($terrain, $status, $logs);
        }

        /** @var Cell $cell */
        $candidates = $terrain->findByTypes([Village::TYPE, Town::TYPE, City::TYPE, Metropolis::TYPE]);

        $monsterCells = $terrain->findByAttribute(CellConst::IS_MONSTER);
        $occurProbability = self::OCCUR_PROBABILITY / pow(2, $monsterCells->count());

        foreach ($candidates as $cell) {

            if ($occurProbability <= Rand::mt_rand_float()) {
                continue;
            }

            $aroundCells = $terrain->getAroundCells($cell->getPoint(), 1, true);
            $seasideCells = $aroundCells->filter(function ($cell) {
                return !$cell::ATTRIBUTE[CellConst::IS_LAND];
            });

            if ($seasideCells->count() <= 0) {
                continue;
            }

            $monster = $appearableMonsters->random();
            /** @var Monster $monsterCell */
            $monsterCell = new $monster(point: $cell->getPoint(), elevation: $cell->getElevation(), remain_move_times: 0);
            $logs->add(new AppearMonsterLog($island, $cell, $monsterCell));

            $terrain->setCell($monsterCell);
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
