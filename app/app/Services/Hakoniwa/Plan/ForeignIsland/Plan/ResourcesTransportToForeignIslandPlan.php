<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\ResourcesTransportationLog;
use App\Services\Hakoniwa\Plan\ResourcesTransportationPlan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class ResourcesTransportToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $fromTerrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        $amount = $this->plan->getAmount() * ResourcesTransportationPlan::UNIT;

        $toStatus->setResources($toStatus->getResources() + $amount);

        if ($seaCells->count() >= 1) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->random();
            $toTerrain->setCell($seaCell->getPoint(), new TransportShip(point: $seaCell->getPoint(), elevation: $seaCell->getElevation()));
        }

        $fromLogs->add(new ResourcesTransportationLog($toIsland, $amount, true));
        $toLogs->add(new ResourcesTransportationLog($fromIsland, $amount, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
