<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
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
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const IS_FIRING = true;
    public const ACCURACY = 1;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;
    protected bool $isFiring = self::IS_FIRING;

    public function getAccuracy(): int
    {
        return self::ACCURACY;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        return parent::execute($island, $terrain, $status, $turn, $foreignIslandTargetedPlans);
    }
}
