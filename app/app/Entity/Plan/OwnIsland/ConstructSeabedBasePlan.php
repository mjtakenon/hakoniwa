<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\MissileFireable\SeabedBase;
use App\Entity\Cell\Others\Sea;
use App\Entity\Log\LogConst;
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
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class ConstructSeabedBasePlan extends Plan
{
    public const KEY = 'construct_seabed_base';

    public const NAME = '海底基地建設';
    public const PRICE = 2000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::CONSTRUCT_SEABED_BASE_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected int $executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;

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

        if ($cell::TYPE !== Sea::TYPE) {
            $logs->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $terrain->setCell($this->point, new SeabedBase(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteLog($island, $this, LogConst::VISIBILITY_PRIVATE));
        $seabedBase = deep_copy($this);
        $seabedBase->point = new Point('?', '?');
        $logs->add(new ExecuteLog($island, $seabedBase));
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
