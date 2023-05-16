<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\HasPopulation\City;
use App\Services\Hakoniwa\Cell\HasPopulation\Metropolis;
use App\Services\Hakoniwa\Cell\HasPopulation\Town;
use App\Services\Hakoniwa\Cell\HasPopulation\Village;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\Monster\MonsterConst;
use App\Services\Hakoniwa\Log\AppearMonsterLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;

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

        $monsterCells = $terrain->findByAttribute(CellTypeConst::IS_MONSTER);
        $occurProbability = self::OCCUR_PROBABILITY / pow(2, $monsterCells->count());

        foreach ($candidates as $cell) {

            if ($occurProbability <= Rand::mt_rand_float()) {
                continue;
            }

            $aroundCells = $terrain->getAroundCells($cell->getPoint(), 1, true);
            $seasideCells = $aroundCells->filter(function ($cell) {
                return !$cell::ATTRIBUTE[CellTypeConst::IS_LAND];
            });

            if ($seasideCells->count() <= 0) {
                continue;
            }

            $monster = $appearableMonsters->random();
            /** @var Monster $monsterCell */
            $monsterCell = new $monster(point: $cell->getPoint(), remain_move_times: 0);
            $logs->add(new AppearMonsterLog($island, $cell, $monsterCell));

            $terrain->setCell($cell->getPoint(), $monsterCell);
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
