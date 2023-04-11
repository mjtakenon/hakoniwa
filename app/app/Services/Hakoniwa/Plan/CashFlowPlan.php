<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\ExecuteLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class CashFlowPlan extends Plan
{
    public const KEY = 'cash_flow';

    public const NAME = '資金繰り';
    public const PRICE = -10;
    public const PRICE_STRING = '(+' . self::PRICE*-1 . '億円)';
    public const USE_POINT = false;

    public function __construct(Point $point = (new Point(0,0)), int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn): PlanExecuteResult
    {
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $turn, $this));
        return new PlanExecuteResult($terrain, $status, $logs, true);
    }
}
