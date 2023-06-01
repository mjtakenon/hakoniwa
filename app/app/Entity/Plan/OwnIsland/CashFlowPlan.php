<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class CashFlowPlan extends Plan
{
    public const KEY = 'cash_flow';

    public const NAME = '資金繰り';
    public const PRICE = -10;
    public const PRICE_STRING = '(+' . self::PRICE*-1 . '億円)';
    public const USE_POINT = false;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $usePoint = self::USE_POINT;

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
