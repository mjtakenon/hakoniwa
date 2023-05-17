<?php

namespace App\Entity\Event\ForeignIsland;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Cell\Ship\CombatantShip;
use App\Entity\Log\AbortReturnLog;
use App\Entity\Log\AbortReturnNotFoundLog;
use App\Entity\Log\Logs;
use App\Entity\Log\ReturnLog;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

class ReturnShipToAffiliationIsland extends ForeignIslandEvent
{
    public function execute(Island $fromIsland, ?Island $toIsland, Terrain $fromTerrain, ?Terrain $toTerrain, Status $fromStatus, ?Status $toStatus, Turn $turn): ForeignIslandEventResult
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

            return new ForeignIslandEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $seaCells = $toTerrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        // 元の島に空いているセルがなければログだけ出してスキップする
        if ($seaCells->isEmpty()) {
            $fromLogs->add(new AbortReturnLog($fromIsland, $this->cell));
            return new ForeignIslandEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
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

        return new ForeignIslandEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
