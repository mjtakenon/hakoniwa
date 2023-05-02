<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoDevelopmentPointsLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

class ExcavationPlan extends Plan
{
    public const KEY = 'excavation';

    public const NAME = '掘削';
    public const PRICE = 200;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = true;
    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::EXCAVATION_AVAILABLE_POINTS;

    public function __construct(Point $point, int $amount = 1, ?int $targetIsland = null)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
        $this->executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $otherIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();
        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $logs->add(new AbortNoDevelopmentPointsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (!in_array($cell::TYPE, array_merge([Shallow::TYPE, Mountain::TYPE], self::CONSTRUCTABLE_CELLS), true)) {
            $logs->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($cell::TYPE === Shallow::TYPE) {
            $terrain->setCell($this->point, new Sea(point: $this->point));
        } else if ($cell::TYPE === Mountain::TYPE) {
            $terrain->setCell($this->point, new Wasteland(point: $this->point));
        } else {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        }

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $turn, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
