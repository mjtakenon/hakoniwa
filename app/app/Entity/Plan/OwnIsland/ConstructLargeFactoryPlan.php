<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\FundsProduction\Factory;
use App\Entity\Cell\FundsProduction\LargeFactory;
use App\Entity\Log\LogRow\AbortInvalidCellLog;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\AbortNoDevelopmentPointsLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class ConstructLargeFactoryPlan extends Plan
{
    public const KEY = 'construct_large_factory';

    public const NAME = '工場拡張';
    public const PRICE = 1000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::CONSTRUCT_LARGE_FACTORY_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $logs->add(new AbortNoDevelopmentPointsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if ($cell::TYPE !== Factory::TYPE) {
            $logs->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $terrain->setCell($this->point, new LargeFactory(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
