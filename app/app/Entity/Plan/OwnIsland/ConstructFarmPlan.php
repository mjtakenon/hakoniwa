<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\FoodsProduction\Farm;
use App\Entity\Log\LogRow\AbortInvalidCellLog;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class ConstructFarmPlan extends Plan
{
    public const KEY = 'construct_farm';

    public const NAME = '農場整備';
    public const PRICE = 20;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        if ($status->getFunds() < self::PRICE) {
            $logs = Logs::create()->add(new AbortLackOfFundsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if (!in_array($cell::TYPE, self::CONSTRUCTABLE_CELLS, true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $terrain->setCell($this->point, new Farm(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
