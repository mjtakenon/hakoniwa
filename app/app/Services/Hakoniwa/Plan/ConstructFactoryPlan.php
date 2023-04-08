<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class ConstructFactoryPlan extends Plan
{
    public const KEY = 'construct_factory';

    public const NAME = '工場建設';
    public const PRICE = 20;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    public function __construct(Point $point, int $amount = 0)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
    }

    public function execute(Point $point, int $amount): void
    {
        // TODO: Implement execute() method.
    }
}
