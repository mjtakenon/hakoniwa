<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class GroundLevelingPlan extends Plan
{
    public const KEY = 'ground_leveling';

    public const NAME = '地ならし';
    public const PRICE = 100;

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
