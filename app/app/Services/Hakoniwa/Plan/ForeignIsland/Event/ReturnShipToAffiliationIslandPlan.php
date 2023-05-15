<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland\Event;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\CombatantShip;
use App\Services\Hakoniwa\Log\AbortReturnLog;
use App\Services\Hakoniwa\Log\AbortReturnNotFoundLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\ReturnLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class ReturnShipToAffiliationIslandPlan extends ForeignIslandOccurEvent
{
    public function execute(Island $fromIsland, ?Island $toIsland, Terrain $fromTerrain, ?Terrain $toTerrain, Status $fromStatus, ?Status $toStatus, Turn $turn): ForeignIslandOccurEventResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        // 帰還先の島がない場合、当該セルを消すだけとする
        if (is_null($toIsland)) {
            if ($this->cell->getElevation() === -1) {
                $fromTerrain->setCell($this->cell->getPoint(), new Shallow(point: $this->cell->getPoint()));
            } else {
                $fromTerrain->setCell($this->cell->getPoint(), new Sea(point: $this->cell->getPoint()));
            }
            /** @var CombatantShip $combatantShip */
            $combatantShip = $this->cell;
            $fromLogs->add(new AbortReturnNotFoundLog($combatantShip));

            return new ForeignIslandOccurEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $seaCells = $toTerrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE]);
        });

        // 元の島に空いているセルがなければログだけ出してスキップする
        if ($seaCells->isEmpty()) {
            $fromLogs->add(new AbortReturnLog($fromIsland, $this->cell));
            return new ForeignIslandOccurEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        if ($this->cell->getElevation() === -1) {
            $fromTerrain->setCell($this->cell->getPoint(), new Shallow(point: $this->cell->getPoint()));
        } else {
            $fromTerrain->setCell($this->cell->getPoint(), new Sea(point: $this->cell->getPoint()));
        }

        /** @var Cell $seaCell */
        $seaCell = $seaCells->random();

        $this->cell->setPoint($seaCell->getPoint());
        $this->cell->setElevation($seaCell->getElevation());
        // 帰還ターンは変数に切り出す
        $this->cell->setReturnTurn(null);

        $toTerrain->setCell($this->cell->getPoint(), $this->cell);

        $fromLogs->add(new ReturnLog($toIsland, $this->cell, true));
        $toLogs->add(new ReturnLog($fromIsland, $this->cell, false));

        return new ForeignIslandOccurEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
