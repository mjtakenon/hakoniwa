<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\IMissileFireable;
use App\Services\Hakoniwa\Cell\MissileBase;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\OutOfRegion;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\MissileDisabledToMonsterLog;
use App\Services\Hakoniwa\Log\MissileFiringLog;
use App\Services\Hakoniwa\Log\MissileHitToMonsterLog;
use App\Services\Hakoniwa\Log\MissileOutOfRegionLog;
use App\Services\Hakoniwa\Log\MissileSelfDestructLog;
use App\Services\Hakoniwa\Log\SoldMonsterCorpseLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class ForeignIslandFiringMissilePlan extends ForeignIslandTargetedPlan
{

    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ForeignIslandExecutePlanResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $targetCells = $toTerrain->getAroundCells($this->plan->getPoint(), $this->plan->getAccuracy(), true);
        $targetCells->add($toTerrain->getCell($this->plan->getPoint()));

        $missileBases = $fromTerrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return array_key_exists(IMissileFireable::class, class_implements($cell));
        });

        $firingCount = 0;
        $amount = $this->plan->getAmount();

        /** @var MissileBase $missileBase */
        foreach ($missileBases as $missileBase) {
            for ($n = 0; $n < $missileBase->getLevel(); $n++) {
                if ($amount === 0) {
                    if ($firingCount >= 1) {
                        $fromLogs->add(new MissileFiringLog($toIsland, $turn, $this->plan->getPoint(), $this->plan, $firingCount));
                    }
                    return new ForeignIslandExecutePlanResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
                }

                if ($fromStatus->getFunds() < $this->plan->getPrice()) {
                    if ($firingCount >= 1) {
                        $fromLogs->add(new MissileFiringLog($toIsland, $turn, $this->plan->getPoint(), $this->plan, $firingCount));
                    }
                    $fromLogs->add(new AbortLackOfFundsLog($fromIsland, $turn, $this->plan->getPoint(), $this->plan));
                    return new ForeignIslandExecutePlanResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
                }
                $fromStatus->setFunds($fromStatus->getFunds() - $this->plan->getPrice());

                /** @var Cell $targetCell */
                $targetCell = $targetCells->random();
                $amount -= 1;
                $firingCount += 1;

                if ($targetCell::TYPE === OutOfRegion::TYPE) {
                    $toLogs->add(new MissileOutOfRegionLog($fromIsland, $turn, $targetCell->getPoint(), $this->plan));
                } else if ($targetCell::ATTRIBUTE[CellTypeConst::IS_MONSTER]) {
                    /** @var Monster $targetCell */
                    // 硬化などによる無効化
                    if ($targetCell->isAttackDisabled()) {
                        $toLogs->add(new MissileDisabledToMonsterLog($fromIsland, $turn, $targetCell, $this->plan));
                        continue;
                    }

                    // 命中
                    $targetCell->setHitPoints($targetCell->getHitPoints() - 1);
                    $toLogs->add(new MissileHitToMonsterLog($fromIsland, $turn, $targetCell, $this->plan));
                    if ($targetCell->getHitPoints() >= 1) {
                        $toTerrain->setCell($targetCell->getPoint(), $targetCell);
                    } else {
                        $toTerrain->setCell($targetCell->getPoint(), new Wasteland(point: $targetCell->getPoint()));

                        $targetCells = $toTerrain->getAroundCells($this->plan->getPoint(), $this->plan->getAccuracy(), true);
                        $targetCells->add($toTerrain->getCell($this->plan->getPoint()));

                        $toLogs->add(new SoldMonsterCorpseLog($turn, $targetCell));
                        $toStatus->setFunds($toStatus->getFunds() + $targetCell->getCorpsePrice());

                        $missileBase->setExperience($missileBase->getExperience() + $targetCell->getExperience());
                        $fromTerrain->setCell($missileBase->getPoint(), $missileBase);
                    }
                } else {
                    $toLogs->add(new MissileSelfDestructLog($fromIsland, $turn, $targetCell->getPoint(), $this->plan));
                }
            }
        }

        if ($firingCount >= 1) {
            $fromLogs->add(new MissileFiringLog($toIsland, $turn, $this->plan->getPoint(), $this->plan, $firingCount));
        }

        return new ForeignIslandExecutePlanResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs);
    }
}