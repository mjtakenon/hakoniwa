<?php

namespace App\Entity\Plan\ForeignIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\MissileFireable\IMissileFireable;
use App\Entity\Cell\MissileFireable\MissileBase;
use App\Entity\Cell\Monster\Monster;
use App\Entity\Cell\Others\OutOfRegion;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\MissileDisabledToMonsterLog;
use App\Entity\Log\LogRow\MissileFiringLog;
use App\Entity\Log\LogRow\MissileHitToMonsterLog;
use App\Entity\Log\LogRow\MissileOutOfRegionLog;
use App\Entity\Log\LogRow\MissileSelfDestructLog;
use App\Entity\Log\LogRow\SoldMonsterCorpseLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanToForeignIslandResult;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use function DeepCopy\deep_copy;

class FiringMissileToForeignIslandPlan extends TargetedToForeignIslandPlan
{
    public function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Achievements $fromAchievements, Achievements $toAchievements, Turn $turn): ExecutePlanToForeignIslandResult
    {
        $fromLogs = Logs::create();
        $toLogs = Logs::create();

        $targetCells = $toTerrain->getAroundCells($this->plan->getPoint(), $this->plan->getAccuracy(), true);
        $targetCells->add($toTerrain->getCell($this->plan->getPoint()));

        $missileBases = $fromTerrain->getCells()->flatten(1)->filter(function ($cell) {
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
                        $fromLogs->add(new MissileFiringLog($toIsland, $this->plan, $firingCount));
                    }
                    return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs, $fromAchievements, $toAchievements);
                }

                if ($fromStatus->getFunds() < $this->plan->getPrice()) {
                    if ($firingCount >= 1) {
                        $fromLogs->add(new MissileFiringLog($toIsland, $this->plan, $firingCount));
                    }
                    $fromLogs->add(new AbortLackOfFundsLog($fromIsland, $this->plan));
                    return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs, $fromAchievements, $toAchievements);
                }
                $fromStatus->setFunds($fromStatus->getFunds() - $this->plan->getPrice());

                /** @var Cell $targetCell */
                $targetCell = $targetCells->random();
                $amount -= 1;
                $firingCount += 1;

                if ($targetCell::TYPE === OutOfRegion::TYPE) {
                    $toLogs->add(new MissileOutOfRegionLog($fromIsland, $targetCell->getPoint(), $this->plan));
                } else if ($targetCell::ATTRIBUTE[CellConst::IS_MONSTER]) {
                    /** @var Monster $targetCell */
                    // 硬化などによる無効化
                    if ($targetCell->isAttackDisabled()) {
                        $toLogs->add(new MissileDisabledToMonsterLog($fromIsland, $targetCell, $this->plan));
                        continue;
                    }

                    // 命中
                    $targetCell->setHitPoints($targetCell->getHitPoints() - 1);
                    $toLogs->add(new MissileHitToMonsterLog($fromIsland, deep_copy($targetCell), $this->plan));
                    if ($targetCell->getHitPoints() >= 1) {
                        $toTerrain->setCell($targetCell->getPoint(), $targetCell);
                    } else {
                        $toTerrain->setCell($targetCell->getPoint(), new Wasteland(point: $targetCell->getPoint()));

                        $targetCells = $toTerrain->getAroundCells($this->plan->getPoint(), $this->plan->getAccuracy(), true);
                        $targetCells->add($toTerrain->getCell($this->plan->getPoint()));

                        $toLogs->add(new SoldMonsterCorpseLog($targetCell));
                        $toStatus->setFunds($toStatus->getFunds() + $targetCell->getCorpsePrice());

                        $missileBase->setExperience($missileBase->getExperience() + $targetCell->getExperience());
                        $fromTerrain->setCell($missileBase->getPoint(), $missileBase);
                    }
                } else {
                    $toLogs->add(new MissileSelfDestructLog($fromIsland, $targetCell->getPoint(), $this->plan));
                }
            }
        }

        if ($firingCount >= 1) {
            $fromLogs->add(new MissileFiringLog($toIsland, $this->plan, $firingCount));
        }

        return new ExecutePlanToForeignIslandResult($fromTerrain, $toTerrain, $fromStatus, $toStatus, $fromLogs, $toLogs, $fromAchievements, $toAchievements);
    }

    private function deep_copy(Monster|Cell $targetCell)
    {
    }
}
