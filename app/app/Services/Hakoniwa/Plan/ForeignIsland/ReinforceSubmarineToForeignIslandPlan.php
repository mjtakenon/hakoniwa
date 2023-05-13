<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\Submarine;
use App\Services\Hakoniwa\Cell\Ship\CombatantShip;
use App\Services\Hakoniwa\Log\AbortInvalidTerrainLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\ReinforceLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class ReinforceSubmarineToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $toTerrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE]);
        });

        if ($seaCells->isEmpty()) {
            $fromLogs->add(new AbortInvalidTerrainLog($fromIsland, $turn, $this->plan));
            return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $amount = min($this->plan->getAmount(), $seaCells->count());

        $seaCells = $seaCells->random($amount);

        $submarines = $fromTerrain->getTerrain()->flatten(1)->filter(function ($cell) use ($fromIsland) {
            /** @var CombatantShip $cell */
            return $cell::TYPE === Submarine::TYPE && $cell->getAffiliationId() === $fromIsland->id;
        })->sort(function ($cell) {
            /** @var CombatantShip $cell */
            return $cell->getDamage();
        })->take($amount);

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
            $submarine->setReturnTurn($turn->turn + 5);

            $toTerrain->setCell($submarine->getPoint(), $submarine);
        }

        $fromLogs->add(new ReinforceLog($toIsland, $turn, $amount, $this->plan, true));
        $toLogs->add(new ReinforceLog($fromIsland, $turn, $amount, $this->plan, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
