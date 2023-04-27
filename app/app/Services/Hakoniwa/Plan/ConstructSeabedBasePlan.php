<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\MissileBase;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\SeabedBase;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoDevelopmentPointsLog;
use App\Services\Hakoniwa\Log\AfforestationLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\LogVisibility;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class ConstructSeabedBasePlan extends Plan
{
    public const KEY = 'construct_seabed_base';

    public const NAME = '海底基地建設';
    public const PRICE = 2000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = true;
    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::CONSTRUCT_SEABED_BASE_AVAILABLE_POINTS;

    public function __construct(Point $point, int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
        $this->executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn): ExecutePlanResult
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

        if ($cell::TYPE !== Sea::TYPE) {
            $logs->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $terrain->setCell($this->point, new SeabedBase(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $turn, $this->point, $this, LogVisibility::VISIBILITY_PRIVATE));
        $logs->add(new ExecuteCellLog($island, $turn, new Point('?', '?'), $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}