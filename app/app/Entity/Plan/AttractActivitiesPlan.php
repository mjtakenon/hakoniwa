<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class AttractActivitiesPlan extends Plan
{
    public const KEY = 'attract_activities';

    public const NAME = '誘致活動';
    public const PRICE = 1000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const AMOUNT_STRING = '(:amount:回実施)';
    public const USE_POINT = false;
    public const USE_AMOUNT = true;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useAmount = self::USE_AMOUNT;
    protected string $amountString = self::AMOUNT_STRING;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $this->amount = 0;
            $logs->add(new AbortLackOfFundsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        /** @var Cell $cell */
        $hasPopulationCells = $terrain->findByAttribute(CellConst::HAS_POPULATION);

        foreach ($hasPopulationCells as $cell) {
            $addPopulation = random_int(1, 3) * 100;
            $cell->setPopulation($cell->getPopulation() + $addPopulation);
        }

        $this->amount -= 1;

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
