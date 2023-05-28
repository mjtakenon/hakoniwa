<?php

namespace App\Entity\Event\Disaster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Monster\Levinoth;
use App\Entity\Cell\Monster\MonsterConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Log\LogRow\AppearMonsterLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;

class AppearanceLevinoth implements IDisaster
{
    const OCCUR_PROBABILITY = 1;

    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult
    {
        $logs = Logs::create();

        if (self::OCCUR_PROBABILITY <= Rand::mt_rand_float()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        // 規定人口未満の場合、スポーンしない
        if ($status->getPopulation() < MonsterConst::APPEARANCE_POPULATION_LV5) {
            return new DisasterResult($terrain, $status, $logs);
        }

        // 既に島にリヴァイノスが存在する場合、スポーンしない
        if ($terrain->findByTypes([Levinoth::TYPE])->isNotEmpty()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        $candidates = $terrain->FindByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($candidates->isEmpty()) {
            return new DisasterResult($terrain, $status, $logs);
        }

        /** @var Cell $cell */
        $cell = $candidates->random();

        $level = max(($status->getDevelopmentPoints() / 500000), 20);
        $hitPoints = Levinoth::DEFAULT_HIT_POINTS + $level;
        $levinoth = new Levinoth(point: $cell->getPoint(), remain_move_times: 0, level: $level, hit_points: $hitPoints, elevation: $cell->getElevation());
        $logs->add(new AppearMonsterLog($island, $cell, $levinoth));

        $terrain->setCell($cell->getPoint(), $levinoth);

        return LevinothFleetsInvasion::occur($island, $terrain, $status, $turn);
    }
}