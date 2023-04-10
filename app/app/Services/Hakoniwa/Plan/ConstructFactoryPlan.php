<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Services\Hakoniwa\Util\Point;

class ConstructFactoryPlan extends Plan
{
    public const KEY = 'construct_factory';

    public const NAME = '工場建設';
    public const PRICE = 20;
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

    public function execute(Island $island, IslandTerrain $islandTerrain, IslandStatus $islandStatus): void
    {
        // TODO: Implement execute() method.
    }
}
