<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\IMissileFireable;
use App\Services\Hakoniwa\Cell\Lake;
use App\Services\Hakoniwa\Cell\Mine;
use App\Services\Hakoniwa\Cell\MissileBase;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\OutOfRegion;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\SeabedBase;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoMissileBaseLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\MissileFiringLog;
use App\Services\Hakoniwa\Log\MissileHitToMonsterLog;
use App\Services\Hakoniwa\Log\MissileOutOfRegionLog;
use App\Services\Hakoniwa\Log\MissileSelfDestructLog;
use App\Services\Hakoniwa\Log\SoldMonsterCorpseLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class FiringHighAccuracyMissilePlan extends Plan
{
    public const KEY = 'firing_high_accuracy_missile';

    public const NAME = '高精度ミサイル発射';
    public const PRICE = 50;
    public const PRICE_STRING = '(数量x' . self::PRICE . ' 億円)';
    public const USE_POINT = true;
    public const USE_AMOUNT = true;
    public const IS_FIRING = true;

    public function __construct(Point $point, int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
        $this->useAmount = self::USE_AMOUNT;
        $this->isFiring = self::IS_FIRING;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn): ExecutePlanResult
    {
        // TODO: 他の島の場合の考慮
        $targetCells = $terrain->getAroundCells($this->point, 1, true);
        $targetCells->add($terrain->getCell($this->point));

        $logs = Logs::create();
        $missileBases = $terrain->getTerrain()->flatten(1)->filter(function($cell) {
            /** @var Cell $cell */
            return array_key_exists(IMissileFireable::class, class_implements($cell));
        });

        if ($this->amount === 0) {
            $this->amount = PHP_INT_MAX;
        }

        $firingCount = 0;

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
                    $targetCell->setHitPoints($targetCell->getHitPoints()-1);
                    $logs->add(new MissileHitToMonsterLog($island, $turn, $targetCell, $this));
                    if ($targetCell->getHitPoints() >= 1) {
                        $terrain->setCell($targetCell->getPoint(), $targetCell);
                    } else {
                        $terrain->setCell($targetCell->getPoint(), new Wasteland(point: $targetCell->getPoint()));

                        $targetCells = $terrain->getAroundCells($this->point, 2, true);
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
