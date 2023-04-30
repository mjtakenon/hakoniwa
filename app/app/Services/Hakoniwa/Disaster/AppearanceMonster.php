<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\City;
use App\Services\Hakoniwa\Cell\Metropolis;
use App\Services\Hakoniwa\Cell\Monster\Inora;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\Monster\MonsterConst;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Town;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AppeardMonsterLog;
use App\Services\Hakoniwa\Log\DestructionByEarthquakeLog;
use App\Services\Hakoniwa\Log\DestructionByFireLog;
use App\Services\Hakoniwa\Log\DestructionByMeteoriteLog;
use App\Services\Hakoniwa\Log\OccurEarthquakeLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\OccurMeteoriteLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use App\Services\Hakoniwa\Util\Rand;
use Illuminate\Support\Collection;

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
        $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return in_array($cell::TYPE, [Village::TYPE, Town::TYPE, City::TYPE, Metropolis::TYPE]);
        });

        $monsterCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::IS_MONSTER];
        });
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
            $logs->add(new AppeardMonsterLog($island, $turn, $cell, $monsterCell));

            $terrain->setCell($cell->getPoint(), $monsterCell);
        }

        return new DisasterResult($terrain, $status, $logs);
    }
}
