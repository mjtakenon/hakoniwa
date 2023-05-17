<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Farm;
use App\Entity\Cell\FarmDome;
use App\Entity\Log\AbortInvalidCellLog;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\AbortNoDevelopmentPointsLog;
use App\Entity\Log\ExecuteCellLog;
use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class ConstructFarmDomePlan extends Plan
{
    public const KEY = 'construct_farm_dome';

    public const NAME = '農場ドーム化';
    public const PRICE = 2000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::CONSTRUCT_FARM_DOME_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $logs->add(new AbortNoDevelopmentPointsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($cell::TYPE !== Farm::TYPE) {
            $logs->add(new AbortInvalidCellLog($island, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $terrain->setCell($this->point, new FarmDome(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}