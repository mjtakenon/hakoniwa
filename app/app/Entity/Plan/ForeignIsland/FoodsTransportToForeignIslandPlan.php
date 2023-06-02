<?php

namespace App\Entity\Plan\ForeignIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\Cell;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\TransportShip;
use App\Entity\Log\LogRow\FoodsTransportationLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanToForeignIslandResult;
use App\Entity\Plan\OwnIsland\FoodsTransportationPlan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

class FoodsTransportToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Achievements $fromAchievements, Achievements $toAchievements, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $fromTerrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        $amount = $this->plan->getAmount() * FoodsTransportationPlan::UNIT;

        $toStatus->setFoods($toStatus->getFoods() + $amount);

        if ($seaCells->count() >= 1) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->random();
            $toTerrain->setCell($seaCell->getPoint(), new TransportShip(point: $seaCell->getPoint(), elevation: $seaCell->getElevation()));
        }

        $fromLogs->add(new FoodsTransportationLog($toIsland, $amount, true));
        $toLogs->add(new FoodsTransportationLog($fromIsland, $amount, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs, $fromAchievements, $toAchievements);
    }
}
