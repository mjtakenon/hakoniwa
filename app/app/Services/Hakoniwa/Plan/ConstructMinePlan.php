<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class ConstructMinePlan extends Plan
{
    public const KEY = 'construct_mine';

    public const NAME = '採掘場整備';
    public const PRICE = 300;

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
