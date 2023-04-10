<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class ConstructMinePlan extends Plan
{
    public const KEY = 'construct_mine';

    public const NAME = '採掘場整備';
    public const PRICE = 300;
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

    public function execute(Terrain $terrain, Status $status): PlanExecuteResult
    {
        return new PlanExecuteResult($terrain, $status);
    }
}
