<?php

namespace App\Entity\Plan;

use App\Entity\Cell\HasWoods\Forest;
use App\Entity\Cell\Others\Plain;
use App\Entity\Log\LogRow\AbortInvalidCellLog;
use App\Entity\Log\LogRow\DeforestationLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class DeforestationPlan extends Plan
{
    public const KEY = 'deforestation';

    public const NAME = '伐採';
    public const PRICE = 0;
    public const PRICE_STRING = '(無料)';

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if (!in_array($cell::TYPE, [Forest::TYPE], true)) {
            $logs->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $amount = $cell->getWoods();
        $terrain->setCell($this->point, new Plain(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $status->setResources($status->getResources() + ($amount * Forest::WOODS_TO_RESOURCES_COEF));
        $logs->add(new DeforestationLog($amount * Forest::WOODS_TO_RESOURCES_COEF));
        $logs->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
