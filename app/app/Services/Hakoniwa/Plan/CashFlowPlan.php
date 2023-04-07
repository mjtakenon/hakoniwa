<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class CashFlowPlan extends Plan
{
    public const KEY = 'cash_flow';

    public const NAME = '資金繰り';
    public const PRICE = -10;

    public function __construct()
    {
        parent::__construct();
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
    }

    public function getPrice(): string
    {
        return '(+' . abs(self::PRICE) . '億円)';
    }

    public function execute(Point $point, int $amount): void
    {
        // TODO: Implement execute() method.
    }
}
