<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\Others\Plain;
use App\Entity\Log\LogRow\AbortInvalidCellLog;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\LogRow\FindBuriedTreasureLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class GradingPlan extends Plan
{
    public const KEY = 'grading';

    public const NAME = '整地';
    public const PRICE = 5;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    private const FIND_BURIED_TREASURE_PROBABILITY = 0.01;

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

        if (!in_array($cell::TYPE, self::GRADABLE_CELLS, true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $terrain->setCell($this->point, new Plain(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $this));

        // 一定確率で埋蔵金を見つける
        if (self::FIND_BURIED_TREASURE_PROBABILITY <= Rand::mt_rand_float()) {
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
        }

        $amount = random_int(100, 1000);
        $logs->add(new FindBuriedTreasureLog($island, $this, $amount));
        $status->setFunds($status->getFunds() + $amount);
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
