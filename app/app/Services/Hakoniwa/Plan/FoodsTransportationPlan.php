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
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortLackOfFoodsLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoMissileBaseLog;
use App\Services\Hakoniwa\Log\AbortNoTransportShipLog;
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
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class FoodsTransportationPlan extends Plan
{
    public const KEY = 'foods_transportation';

    public const NAME = '食料輸送';
    public const PRICE = 0;
    public const PRICE_STRING = '(数量x10000 ㌧)';
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const USE_POINT = false;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $transportShips = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return $cell::TYPE === TransportShip::TYPE;
        });

        if ($this->amount === 0) {
            $this->amount = 100;
        }

        if ($transportShips->isEmpty()) {
            $logs->add(new AbortNoTransportShipLog($island, $turn, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getFoods() < 10000 * $this->amount) {
            $logs->add(new AbortLackOfFoodsLog($island, $turn, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        // 対象が自島でない場合、後で処理する
        if ($this->getTargetIsland() === $island->id) {
        }

        $foreignIslandTargetedPlans->add(new ForeignIslandFiringMissilePlan(
            $island->id,
            $this->getTargetIsland(),
            deep_copy($this),
        ));
        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, true);

//        $firingCount = 0;
//
//        /** @var MissileBase $missileBase */
//        foreach ($missileBases as $missileBase) {
//            for ($n = 0; $n < $missileBase->getLevel(); $n++) {
//                if ($this->amount === 0) {
//                    if ($firingCount >= 1) {
//                        $logs->add(new MissileFiringLog($island, $turn, $this->point, $this, $firingCount));
//                    }
//                    $this->amount = 0;
//                    return new ExecutePlanResult($terrain, $status, $logs, true);
//                }
//
//                if ($status->getFunds() < self::PRICE) {
//                    if ($firingCount >= 1) {
//                        $logs->add(new MissileFiringLog($island, $turn, $this->point, $this, $firingCount));
//                    }
//                    $logs->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
//                    $this->amount = 0;
//                    return new ExecutePlanResult($terrain, $status, $logs, true);
//                }
//                $status->setFunds($status->getFunds() - self::PRICE);
//
//                /** @var Cell $targetCell */
//                $targetCell = $targetCells->random();
//                $this->amount -= 1;
//                $firingCount += 1;
//
//                if ($targetCell::TYPE === OutOfRegion::TYPE) {
//                    $logs->add(new MissileOutOfRegionLog($island, $turn, $targetCell->getPoint(), $this));
//                } else if ($targetCell::ATTRIBUTE[CellTypeConst::IS_MONSTER]) {
//                    /** @var Monster $targetCell */
//                    // 硬化などによる無効化
//                    if ($targetCell->isAttackDisabled()) {
//                        $logs->add(new MissileDisabledToMonsterLog($island, $turn, $targetCell, $this));
//                        continue;
//                    }
//
//                    // 命中
//                    $targetCell->setHitPoints($targetCell->getHitPoints() - 1);
//                    $logs->add(new MissileHitToMonsterLog($island, $turn, $targetCell, $this));
//                    if ($targetCell->getHitPoints() >= 1) {
//                        $terrain->setCell($targetCell->getPoint(), $targetCell);
//                    } else {
//                        $terrain->setCell($targetCell->getPoint(), new Wasteland(point: $targetCell->getPoint()));
//
//                        $targetCells = $terrain->getAroundCells($this->point, $this->getAccuracy(), true);
//                        $targetCells->add($terrain->getCell($this->point));
//
//                        $logs->add(new SoldMonsterCorpseLog($turn, $targetCell));
//                        $status->setFunds($status->getFunds() + $targetCell->getCorpsePrice());
//
//                        $missileBase->setExperience($missileBase->getExperience() + $targetCell->getExperience());
//                        $terrain->setCell($missileBase->getPoint(), $missileBase);
//                    }
//                } else {
//                    $logs->add(new MissileSelfDestructLog($island, $turn, $targetCell->getPoint(), $this));
//                }
//            }
//        }
//
//        if ($firingCount >= 1) {
//            $logs->add(new MissileFiringLog($island, $turn, $this->point, $this, $firingCount));
//        }
        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
