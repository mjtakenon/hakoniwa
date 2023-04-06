<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class LandfillPlan extends Plan
{
    public const KEY = 'landfill';

    public const NAME = '埋め立て';
    public const PRICE = 150;

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
