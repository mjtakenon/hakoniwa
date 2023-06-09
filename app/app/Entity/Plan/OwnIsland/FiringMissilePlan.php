<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\MissileFireable\IMissileFireable;
use App\Entity\Cell\MissileFireable\MissileBase;
use App\Entity\Cell\Monster\Monster;
use App\Entity\Cell\Others\OutOfRegion;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\AbortNoMissileBaseLog;
use App\Entity\Log\LogRow\MissileDisabledToMonsterLog;
use App\Entity\Log\LogRow\MissileFiringLog;
use App\Entity\Log\LogRow\MissileHitToMonsterLog;
use App\Entity\Log\LogRow\MissileOutOfRegionLog;
use App\Entity\Log\LogRow\MissileSelfDestructLog;
use App\Entity\Log\LogRow\SoldMonsterCorpseLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\ForeignIsland\FiringMissileToForeignIslandPlan;
use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class FiringMissilePlan extends Plan
{
    public const KEY = 'firing_missile';

    public const NAME = 'ミサイル発射';
    public const PRICE = 20;
    public const PRICE_STRING = '(数量x' . self::PRICE . '億円)';
    public const DEFAULT_AMOUNT_STRING = '(無制限)';
    public const AMOUNT_STRING = '(:amount:発発射)';
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const IS_FIRING = true;
    public const ACCURACY = 2;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;
    protected string $amountString = self::AMOUNT_STRING;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;
    protected bool $isFiring = self::IS_FIRING;

    public function getAccuracy(): int
    {
        return self::ACCURACY;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $targetCells = $terrain->getAroundCells($this->point, $this->getAccuracy(), true);
        $targetCells->add($terrain->getCell($this->point));

        $missileBases = $terrain->getCells()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return array_key_exists(IMissileFireable::class, class_implements($cell));
        });

        if ($this->amount === 0) {
            $this->amount = PHP_INT_MAX;
        }

        if ($missileBases->isEmpty()) {
            $logs->add(new AbortNoMissileBaseLog($island, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        // 対象が自島でない場合、後で処理する
        if ($this->getTargetIsland() !== $island->id) {
            $foreignIslandTargetedPlans->add(new FiringMissileToForeignIslandPlan(
                $island->id,
                $this->getTargetIsland(),
                deep_copy($this),
            ));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
        }

        $firingCount = 0;

        /** @var MissileBase $missileBase */
        foreach ($missileBases as $missileBase) {
            for ($n = 0; $n < $missileBase->getLevel(); $n++) {
                if ($this->amount === 0) {
                    if ($firingCount >= 1) {
                        $logs->add(new MissileFiringLog($island, $this, $firingCount));
                    }
                    $this->amount = 0;
                    return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
                }

                if ($status->getFunds() < self::PRICE) {
                    if ($firingCount >= 1) {
                        $logs->add(new MissileFiringLog($island, $this, $firingCount));
                    }
                    $logs->add(new AbortLackOfFundsLog($island, $this));
                    $this->amount = 0;
                    return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
                }
                $status->setFunds($status->getFunds() - self::PRICE);

                /** @var Cell $targetCell */
                $targetCell = $targetCells->random();
                $this->amount -= 1;
                $firingCount += 1;

                if ($targetCell::TYPE === OutOfRegion::TYPE) {
                    $logs->add(new MissileOutOfRegionLog($island, $targetCell->getPoint(), $this));
                } else if ($targetCell::ATTRIBUTE[CellConst::IS_MONSTER]) {
                    /** @var Monster $targetCell */
                    // 硬化などによる無効化
                    if ($targetCell->isAttackDisabled()) {
                        $logs->add(new MissileDisabledToMonsterLog($island, $targetCell, $this));
                        continue;
                    }

                    // 命中
                    $targetCell->setHitPoints($targetCell->getHitPoints() - 1);
                    $logs->add(new MissileHitToMonsterLog($island, deep_copy($targetCell), $this));
                    if ($targetCell->getHitPoints() >= 1) {
                        $terrain->setCell($targetCell->getPoint(), $targetCell);
                    } else {
                        $terrain->setCell($targetCell->getPoint(), new Wasteland(point: $targetCell->getPoint()));

                        $targetCells = $terrain->getAroundCells($this->point, $this->getAccuracy(), true);
                        $targetCells->add($terrain->getCell($this->point));

                        $logs->add(new SoldMonsterCorpseLog($targetCell));
                        $status->setFunds($status->getFunds() + $targetCell->getCorpsePrice());

                        $missileBase->setExperience($missileBase->getExperience() + $targetCell->getExperience());
                        $terrain->setCell($missileBase->getPoint(), $missileBase);
                    }
                } else {
                    $logs->add(new MissileSelfDestructLog($island, $targetCell->getPoint(), $this));
                }
            }
        }

        if ($firingCount >= 1) {
            $logs->unshift(new MissileFiringLog($island, $this, $firingCount));
        }
        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
