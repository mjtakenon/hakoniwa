<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Services\Hakoniwa\Util\Point;

class CashFlowPlan extends Plan
{
    public const KEY = 'cash_flow';

    public const NAME = '資金繰り';
    public const PRICE = -10;
    public const PRICE_STRING = '(+' . self::PRICE*-1 . '億円)';
    public const USE_POINT = false;

    public function __construct(Point $point = (new Point(0,0)), int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
    }

    public function execute(Island $island, IslandTerrain $islandTerrain, IslandStatus $islandStatus): void
    {
        // TODO: Implement execute() method.
    }
}
