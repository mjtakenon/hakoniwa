<?php

namespace App\Entity\Event\ForeignIsland;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\CombatantShip;
use App\Entity\Log\LogRow\AbortReturnLog;
use App\Entity\Log\LogRow\AbortReturnNotFoundLog;
use App\Entity\Log\LogRow\ReturnLog;
use App\Entity\Log\Logs;
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

        /** @var CombatantShip $combatantShip */
        $combatantShip = $this->cell;

        // 帰還先の島がない場合、当該セルを消すだけとする
        if (is_null($toIsland)) {
            $fromTerrain->setCell(CellConst::getDefaultCell($combatantShip->getPoint(), $combatantShip->getElevation()));
            $fromLogs->add(new AbortReturnNotFoundLog($combatantShip));

            return new ForeignIslandEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }

        $seaCells = $toTerrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        // 元の島に空いているセルがなければログだけ出してスキップする
        if ($seaCells->isEmpty()) {
            $fromLogs->add(new AbortReturnLog($fromIsland, $combatantShip));
            return new ForeignIslandEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
        }
        $fromTerrain->setCell(CellConst::getDefaultCell($combatantShip->getPoint(), $combatantShip->getElevation()));

        /** @var Cell $seaCell */
        $seaCell = $seaCells->random();

        $combatantShip->setPoint($seaCell->getPoint());
        $combatantShip->setElevation($seaCell->getElevation());
        $combatantShip->setReturnTurn(null);
        // 途中で島名が変わったときのことを考慮し、艦隊名を更新
        $combatantShip->setAffiliationName($toIsland->name);

        $toTerrain->setCell($combatantShip);

        $fromLogs->add(new ReturnLog($toIsland, $combatantShip, true));
        $toLogs->add(new ReturnLog($fromIsland, $combatantShip, false));

        return new ForeignIslandEventResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}
