<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\Battleship;
use App\Services\Hakoniwa\Log\AbortInvalidTerrainLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\ExecuteLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;

class ConstructBattleshipPlan extends Plan
{
    public const KEY = 'construct_battleship';

    public const NAME = '戦艦建造';
    public const PRICE = 500;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const AMOUNT_STRING = '(:amount:回実施)';
    public const DEFAULT_AMOUNT_STRING = '(1回実施)';
    public const USE_POINT = false;
    public const USE_AMOUNT = true;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useAmount = self::USE_AMOUNT;
    protected string $amountString = self::AMOUNT_STRING;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        if ($status->getFunds() < self::PRICE) {
            $this->amount = 0;
            $logs = Logs::create()->add(new AbortLackOfFundsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->count() <= 0) {
            $this->amount = 0;
            $logs = Logs::create()->add(new AbortInvalidTerrainLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $this->amount -= 1;

        /** @var Cell $cell */
        $cell = $seaCells->random();

        $terrain->setCell($cell->getPoint(), new Battleship(
            point: $cell->getPoint(),
            elevation: $cell->getElevation(),
            affiliation_id: $island->id,
            affiliation_name: $island->name,
        ));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
