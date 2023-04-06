<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class GroundLevelingPlan extends Plan
{
    public const KEY = 'ground_leveling';

    public const NAME = '地ならし';
    public const PRICE = 100;

    public function getName(): string
    {
        return self::NAME;
    }

    public function getPrice(): string
    {
        return '(' . self::PRICE . '億円)';
    }

    public function getKey(): string
    {
        return self::KEY;
    }

    public function execute(Point $point, int $amount): void
    {
        // TODO: Implement execute() method.
    }
}
