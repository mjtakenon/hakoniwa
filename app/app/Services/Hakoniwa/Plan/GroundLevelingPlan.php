<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class GroundLevelingPlan extends Plan
{
    public const KEY = 'ground_leveling';

    public const NAME = '地ならし';
    public const PRICE = 100;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = true;

    public function __construct(Point $point, int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
    }

    public function execute(Point $point, int $amount): void
    {
        // TODO: Implement execute() method.
    }
}
