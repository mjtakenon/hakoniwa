<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\Cell;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\Submarine;
use App\Entity\Log\LogRow\AbortInvalidTerrainLog;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\AbortNoDevelopmentPointsLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class ConstructSubmarinePlan extends Plan
{
    public const KEY = 'construct_submarine';

    public const NAME = '潜水艦建造';
    public const PRICE = 2000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const AMOUNT_STRING = '(:amount:回実施)';
    public const DEFAULT_AMOUNT_STRING = '(1回実施)';
    public const USE_POINT = false;
    public const USE_AMOUNT = true;
    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::CONSTRUCT_SUBMARINE_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useAmount = self::USE_AMOUNT;
    protected string $amountString = self::AMOUNT_STRING;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();
        if ($status->getFunds() < self::PRICE) {
            $this->amount = 0;
            $logs->add(new AbortLackOfFundsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $this->amount = 0;
            $logs->add(new AbortNoDevelopmentPointsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->count() <= 0) {
            $this->amount = 0;
            $logs->add(new AbortInvalidTerrainLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $this->amount -= 1;

        /** @var Cell $cell */
        $cell = $seaCells->random();

        $terrain->setCell(new Submarine(
            point: $cell->getPoint(),
            elevation: $cell->getElevation(),
            affiliation_id: $island->id,
            affiliation_name: $island->name,
        ));

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
