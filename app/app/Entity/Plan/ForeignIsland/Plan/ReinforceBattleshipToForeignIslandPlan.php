<?php

namespace App\Entity\Plan\ForeignIsland\Plan;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\CombatantShip;
use App\Entity\Log\AbortInvalidTerrainLog;
use App\Entity\Log\AbortNoShipLog;
use App\Entity\Log\Logs;
use App\Entity\Log\ReinforceLog;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;

class ReinforceBattleshipToForeignIslandPlan extends TargetedToForeignIslandPlan
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

        $battleships = $fromTerrain->findByTypes([Battleship::TYPE])->filter(function ($cell) use ($fromIsland) {
            /** @var CombatantShip $cell */
            return $cell->getAffiliationId() === $fromIsland->id;
        })->sort(function($cell) {
            /** @var CombatantShip $cell */
            return $cell->getDamage();
        });

        $amount = min($this->plan->getAmount(), $seaCells->count(), $battleships->count());
        if ($amount <= 0) {
            $fromLogs->add(new AbortNoShipLog($fromIsland, $this->plan, new Battleship(point: new Point(0,0))));
            return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $seaCells = $seaCells->random($amount);
        $battleships = $battleships->take($amount);

        /** @var Battleship $battleship */
        foreach ($battleships as $battleship) {
            /** @var Cell $seaCell */
            $seaCell = $seaCells->pop();
            if ($battleship->getElevation() === -1) {
                $fromTerrain->setCell($battleship->getPoint(), new Shallow(point: $battleship->getPoint()));
            } else {
                $fromTerrain->setCell($battleship->getPoint(), new Sea(point: $battleship->getPoint()));
            }

            $battleship->setPoint($seaCell->getPoint());
            $battleship->setElevation($seaCell->getElevation());
            // 帰還ターンは変数に切り出す
            $battleship->setReturnTurn($turn->turn + self::DEFAULT_REINFORCE_TURN);

            $toTerrain->setCell($battleship->getPoint(), $battleship);
        }

        $fromLogs->add(new ReinforceLog($toIsland, $amount, $this->plan, true));
        $toLogs->add(new ReinforceLog($fromIsland, $amount, $this->plan, false));

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
