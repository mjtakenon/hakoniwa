<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Lake;
use App\Services\Hakoniwa\Cell\Mine;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\ExecuteLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class GradingPlan extends Plan
{
    public const KEY = 'grading';

    public const NAME = '整地';
    public const PRICE = 5;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = true;

    public const UNGRADABLE_CELL = [
        Sea::TYPE,
        Shallow::TYPE,
        Lake::TYPE,
        Mountain::TYPE,
        Mine::TYPE,
    ];

    public function __construct(Point $point, int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn): PlanExecuteResult
    {
        $cell = $terrain->getCell($this->point);
        if ($status->getFunds() < self::PRICE) {
            $logs = Logs::create()->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            return new PlanExecuteResult($terrain, $status, $logs, false);
        }

        if (in_array($cell::TYPE, self::UNGRADABLE_CELL)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new PlanExecuteResult($terrain, $status, $logs, false);
        }

        $terrain->setCell($this->point, new Plain(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $turn, $this->point, $this));
        return new PlanExecuteResult($terrain, $status, $logs, true);
    }
}
