<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Log\FoodsTransportationLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Plan\FoodsTransportationPlan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class FoodsTransportToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $fromTerrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE]);
        });

        $amount = $this->plan->getAmount() * FoodsTransportationPlan::UNIT;

        $toStatus->setFoods($toStatus->getFoods() + $amount);

        if ($seaCells->count() >= 1) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->random();
            $toTerrain->setCell($seaCell->getPoint(), new TransportShip(point: $seaCell->getPoint(), elevation: $seaCell->getElevation()));
        }

        $fromLogs->add(new FoodsTransportationLog($toIsland, $amount, true));
        $toLogs->add(new FoodsTransportationLog($fromIsland, $amount, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
