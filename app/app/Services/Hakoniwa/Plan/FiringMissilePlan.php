<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\MissileBase\IMissileFireable;
use App\Services\Hakoniwa\Cell\MissileBase\MissileBase;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\OutOfRegion;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoMissileBaseLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\MissileDisabledToMonsterLog;
use App\Services\Hakoniwa\Log\MissileFiringLog;
use App\Services\Hakoniwa\Log\MissileHitToMonsterLog;
use App\Services\Hakoniwa\Log\MissileOutOfRegionLog;
use App\Services\Hakoniwa\Log\MissileSelfDestructLog;
use App\Services\Hakoniwa\Log\SoldMonsterCorpseLog;
use App\Services\Hakoniwa\Plan\ForeignIsland\ForeignIslandFiringMissilePlan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class FiringMissilePlan extends Plan
{
    public const KEY = 'firing_missile';

    public const NAME = 'ミサイル発射';
    public const PRICE = 20;
    public const PRICE_STRING = '(数量x' . self::PRICE . ' 億円)';
    public const USE_POINT = true;
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const IS_FIRING = true;
    public const ACCURACY = 2;

    public function __construct(Point $point, int $amount = 1, ?int $targetIsland = null)
    {
        parent::__construct($point, $amount, $targetIsland);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
        $this->useAmount = self::USE_AMOUNT;
        $this->useTargetIsland = self::USE_TARGET_ISLAND;
        $this->isFiring = self::IS_FIRING;
    }

    public function getAccuracy(): int
    {
        return self::ACCURACY;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $targetCells = $terrain->getAroundCells($this->point, $this->getAccuracy(), true);
        $targetCells->add($terrain->getCell($this->point));

        $missileBases = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return array_key_exists(IMissileFireable::class, class_implements($cell));
        });

        if ($this->amount === 0) {
            $this->amount = PHP_INT_MAX;
        }

        if ($missileBases->isEmpty()) {
            $logs->add(new AbortNoMissileBaseLog($island, $turn, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        // 対象が自島でない場合、後で処理する
        if ($this->getTargetIsland() !== $island->id) {
            $foreignIslandTargetedPlans->add(new ForeignIslandFiringMissilePlan(
                $island->id,
                $this->getTargetIsland(),
                deep_copy($this),
            ));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, true);
        }

        $firingCount = 0;

        /** @var MissileBase $missileBase */
        foreach ($missileBases as $missileBase) {
            for ($n = 0; $n < $missileBase->getLevel(); $n++) {
                if ($this->amount === 0) {
                    if ($firingCount >= 1) {
                        $logs->add(new MissileFiringLog($island, $turn, $this->point, $this, $firingCount));
                    }
                    $this->amount = 0;
                    return new ExecutePlanResult($terrain, $status, $logs, true);
                }

                if ($status->getFunds() < self::PRICE) {
                    if ($firingCount >= 1) {
                        $logs->add(new MissileFiringLog($island, $turn, $this->point, $this, $firingCount));
                    }
                    $logs->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
                    $this->amount = 0;
                    return new ExecutePlanResult($terrain, $status, $logs, true);
                }
                $status->setFunds($status->getFunds() - self::PRICE);

                /** @var Cell $targetCell */
                $targetCell = $targetCells->random();
                $this->amount -= 1;
                $firingCount += 1;

                if ($targetCell::TYPE === OutOfRegion::TYPE) {
                    $logs->add(new MissileOutOfRegionLog($island, $turn, $targetCell->getPoint(), $this));
                } else if ($targetCell::ATTRIBUTE[CellTypeConst::IS_MONSTER]) {
                    /** @var Monster $targetCell */
                    // 硬化などによる無効化
                    if ($targetCell->isAttackDisabled()) {
                        $logs->add(new MissileDisabledToMonsterLog($island, $turn, $targetCell, $this));
                        continue;
                    }

                    // 命中
                    $targetCell->setHitPoints($targetCell->getHitPoints() - 1);
                    $logs->add(new MissileHitToMonsterLog($island, $turn, $targetCell, $this));
                    if ($targetCell->getHitPoints() >= 1) {
                        $terrain->setCell($targetCell->getPoint(), $targetCell);
                    } else {
                        $terrain->setCell($targetCell->getPoint(), new Wasteland(point: $targetCell->getPoint()));

                        $targetCells = $terrain->getAroundCells($this->point, $this->getAccuracy(), true);
                        $targetCells->add($terrain->getCell($this->point));

                        $logs->add(new SoldMonsterCorpseLog($turn, $targetCell));
                        $status->setFunds($status->getFunds() + $targetCell->getCorpsePrice());

                        $missileBase->setExperience($missileBase->getExperience() + $targetCell->getExperience());
                        $terrain->setCell($missileBase->getPoint(), $missileBase);
                    }
                } else {
                    $logs->add(new MissileSelfDestructLog($island, $turn, $targetCell->getPoint(), $this));
                }
            }
        }

        if ($firingCount >= 1) {
            $logs->add(new MissileFiringLog($island, $turn, $this->point, $this, $firingCount));
        }
        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
