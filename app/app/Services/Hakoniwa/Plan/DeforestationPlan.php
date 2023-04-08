<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class DeforestationPlan extends Plan
{
    public const KEY = 'deforestation';

    public const NAME = '伐採';
    public const PRICE = 0;
    public const PRICE_STRING = '(無料)';
    public const USE_POINT = true;

    public function __construct(Point $point, int $amount = 0)
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
