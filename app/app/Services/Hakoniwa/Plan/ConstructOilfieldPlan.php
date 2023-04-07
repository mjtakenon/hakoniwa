<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class ConstructOilfieldPlan extends Plan
{
    public const KEY = 'construct_oilfield';

    public const NAME = '油田発掘';
    public const PRICE = 200;

    public function __construct()
    {
        parent::__construct();
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
    }

    public function getPrice(): string
    {
        return '(' . self::PRICE . '億円)';
    }

    public function execute(Point $point, int $amount): void
    {
        // TODO: Implement execute() method.
    }
}
