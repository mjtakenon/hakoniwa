<?php

namespace App\Entity\Plan\ForeignIsland;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\CombatantShip;
use App\Entity\Cell\Ship\Submarine;
use App\Entity\Log\AbortInvalidTerrainLog;
use App\Entity\Log\AbortNoShipLog;
use App\Entity\Log\Logs;
use App\Entity\Log\ReinforceLog;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;

class ReinforceSubmarineToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    const DEFAULT_REINFORCE_TURN = 5;

    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $toTerrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->isEmpty()) {
            $fromLogs->add(new AbortInvalidTerrainLog($fromIsland, $this->plan));
            return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $submarines = $fromTerrain->findByTypes([Submarine::TYPE])->filter(function ($cell) use ($fromIsland) {
            /** @var CombatantShip $cell */
            return $cell->getAffiliationId() === $fromIsland->id;
        })->sort(function ($cell) {
            /** @var CombatantShip $cell */
            return $cell->getDamage();
        });

        $amount = min($this->plan->getAmount(), $seaCells->count(), $submarines->count());
        if ($amount <= 0) {
            $fromLogs->add(new AbortNoShipLog($fromIsland, $this->plan, new Submarine(point: new Point(0, 0))));
            return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $seaCells = $seaCells->random($amount);
        $submarines = $submarines->take($amount);

        /** @var Submarine $submarine */
        foreach ($submarines as $submarine) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->pop();
            if ($submarine->getElevation() === -1) {
                $fromTerrain->setCell($submarine->getPoint(), new Shallow(point: $submarine->getPoint()));
            } else {
                $fromTerrain->setCell($submarine->getPoint(), new Sea(point: $submarine->getPoint()));
            }

            $submarine->setPoint($seaCell->getPoint());
            $submarine->setElevation($seaCell->getElevation());
            // 帰還ターンは変数に切り出す
            $submarine->setReturnTurn($turn->turn + self::DEFAULT_REINFORCE_TURN);

            $toTerrain->setCell($submarine->getPoint(), $submarine);
        }

        $fromLogs->add(new ReinforceLog($toIsland, $amount, $this->plan, true));
        $toLogs->add(new ReinforceLog($fromIsland, $amount, $this->plan, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
