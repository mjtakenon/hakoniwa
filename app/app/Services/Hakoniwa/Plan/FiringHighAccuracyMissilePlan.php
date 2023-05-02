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
use App\Services\Hakoniwa\Log\MissileDisabledToMonsterLog;
use App\Services\Hakoniwa\Log\MissileFiringLog;
use App\Services\Hakoniwa\Log\MissileHitToMonsterLog;
use App\Services\Hakoniwa\Log\MissileOutOfRegionLog;
use App\Services\Hakoniwa\Log\MissileSelfDestructLog;
use App\Services\Hakoniwa\Log\SoldMonsterCorpseLog;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

class FiringHighAccuracyMissilePlan extends FiringMissilePlan
{
    public const KEY = 'firing_high_accuracy_missile';

    public const NAME = '高精度ミサイル発射';
    public const PRICE = 50;
    public const PRICE_STRING = '(数量x' . self::PRICE . ' 億円)';
    public const USE_POINT = true;
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const IS_FIRING = true;
    public const ACCURACY = 1;

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
        return parent::execute($island, $terrain, $status, $turn, $foreignIslandTargetedPlans);
    }
}
