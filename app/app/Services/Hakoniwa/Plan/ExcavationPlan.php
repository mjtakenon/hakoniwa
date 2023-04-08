<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class ExcavationPlan extends Plan
{
    public const KEY = 'excavation';

    public const NAME = '掘削';
    public const PRICE = 200;
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
