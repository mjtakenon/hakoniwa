<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Mine;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;

class ConstructMinePlan extends Plan
{
    public const KEY = 'construct_mine';

    public const NAME = '採掘場整備';
    public const PRICE = 300;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        if ($status->getFunds() < self::PRICE) {
            $logs = Logs::create()->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (!in_array($cell::TYPE, [Mountain::TYPE], true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $terrain->setCell($this->point, new Mine(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteCellLog($island, $turn, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
