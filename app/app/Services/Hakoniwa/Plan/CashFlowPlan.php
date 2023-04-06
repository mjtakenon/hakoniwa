<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class CashFlowPlan extends Plan
{
    public const KEY = 'cash_flow';

    public const NAME = '資金繰り';
    public const PRICE = -10;

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
