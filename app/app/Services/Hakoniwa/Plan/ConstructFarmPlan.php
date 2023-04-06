<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class ConstructFarmPlan extends Plan
{
    public const KEY = 'construct_farm';

    public const NAME = '農場整備';
    public const PRICE = 20;

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
