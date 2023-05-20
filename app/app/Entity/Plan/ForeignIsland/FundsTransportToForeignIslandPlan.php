<?php

namespace App\Entity\Plan\ForeignIsland;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\TransportShip;
use App\Entity\Log\LogRow\FundsTransportationLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\FundsTransportationPlan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

class FundsTransportToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $fromTerrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        $amount = $this->plan->getAmount() * FundsTransportationPlan::UNIT;

        $toStatus->setFunds($toStatus->getFunds() + $amount);

        if ($seaCells->count() >= 1) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->random();
            $toTerrain->setCell($seaCell->getPoint(), new TransportShip(point: $seaCell->getPoint(), elevation: $seaCell->getElevation()));
        }

        $fromLogs->add(new FundsTransportationLog($toIsland, $amount, true));
        $toLogs->add(new FundsTransportationLog($fromIsland, $amount, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
