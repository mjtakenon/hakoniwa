<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class FiringHighAccuracyMissilePlan extends FiringMissilePlan
{
    public const KEY = 'firing_high_accuracy_missile';

    public const NAME = '高精度ミサイル発射';
    public const PRICE = 50;
    public const PRICE_STRING = '(数量x' . self::PRICE . '億円)';
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
}
