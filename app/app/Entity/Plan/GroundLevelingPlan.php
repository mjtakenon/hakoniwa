<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Others\Plain;
use App\Entity\Log\AbortInvalidCellLog;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\AbortNoDevelopmentPointsLog;
use App\Entity\Log\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class GroundLevelingPlan extends Plan
{
    public const KEY = 'ground_leveling';

    public const NAME = '高速整地';
    public const PRICE = 100;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::GROUND_LEVELING_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected int $executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $logs->add(new AbortNoDevelopmentPointsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (!in_array($cell::TYPE, self::GRADABLE_CELLS, true)) {
            $logs->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $terrain->setCell($this->point, new Plain(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
