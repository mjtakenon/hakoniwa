<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Ship\Submarine;
use App\Services\Hakoniwa\Log\AbortInvalidIslandLog;
use App\Services\Hakoniwa\Log\AbortNoShipLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Plan\ForeignIsland\Plan\ReinforceSubmarineToForeignIslandPlan;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class ReinforceSubmarinePlan extends Plan
{
    public const KEY = 'reinforce_submarine';

    public const NAME = '潜水艦派遣';
    public const PRICE = 0;
    public const PRICE_STRING = '(数量x1隻)';
    public const DEFAULT_AMOUNT_STRING = '(無制限)';
    public const AMOUNT_STRING = '(:amount:隻)';
    public const USE_AMOUNT = true;
    public const USE_POINT = false;
    public const USE_TARGET_ISLAND = true;
    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::REINFORCE_SUBMARINE_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;
    protected string $amountString = self::AMOUNT_STRING;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;
    protected int $executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;
    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $submarines = $terrain->getTerrain()->flatten(1)->filter(function ($cell) use ($island) {
            /** @var Cell $cell */
            return $cell::TYPE === Submarine::TYPE && $cell->getAffiliationId() === $island->id;
        });

        if ($this->amount === 0) {
            $this->amount = PHP_INT_MAX;
        }

        if ($submarines->isEmpty()) {
            $logs->add(new AbortNoShipLog($island, $this, new Submarine(point: new Point(0, 0))));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        // 対象が自島の場合は中止とする
        if ($this->getTargetIsland() === $island->id) {
            $logs->add(new AbortInvalidIslandLog($island, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $this->amount = min($submarines->count(), $this->amount);

        $foreignIslandTargetedPlans->add(new ReinforceSubmarineToForeignIslandPlan(
            $island->id,
            $this->getTargetIsland(),
            deep_copy($this),
        ));

        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
