<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\Battleship;
use App\Services\Hakoniwa\Cell\Ship\CombatantShip;
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Log\AbortInvalidTerrainLog;
use App\Services\Hakoniwa\Log\ReinforceLog;
use App\Services\Hakoniwa\Log\ResourcesTransportationLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Plan\ResourcesTransportationPlan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class ReinforceBattleshipToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $seaCells = $fromTerrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE]);
        });

        if ($seaCells->isEmpty()) {
            $fromLogs->add(new AbortInvalidTerrainLog($fromIsland, $turn, $this->plan));
            return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $amount = min($this->plan->getAmount(), $seaCells->count());

        $seaCells = $seaCells->random($amount);

        $battleShips = $fromTerrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var CombatantShip $cell */
            return $cell::TYPE === Battleship::TYPE;
        })->sort(function($cell) {
            /** @var CombatantShip $cell */
            return $cell->getDamage();
        })->take($amount);

        /** @var Battleship $battleShip */
        foreach ($battleShips as $battleShip) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->pop();
            if ($battleShip->getElevation() === -1) {
                $fromTerrain->setCell($battleShip->getPoint(), new Shallow(point: $battleShip->getPoint()));
            } else {
                $fromTerrain->setCell($battleShip->getPoint(), new Sea(point: $battleShip->getPoint()));
            }

            $battleShip->setPoint($seaCell->getPoint());
            $battleShip->setElevation($seaCell->getElevation());
            // 帰還ターンは変数に切り出す
            $battleShip->setReturnTurn($turn->turn + 5);

            $toTerrain->setCell($battleShip->getPoint(), $battleShip);
        }

        $fromLogs->add(new ReinforceLog($toIsland, $turn, $amount, $this->plan, true));
        $toLogs->add(new ReinforceLog($fromIsland, $turn, $amount, $this->plan, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
