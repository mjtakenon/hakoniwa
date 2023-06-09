<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\CombatantShip;
use App\Entity\Log\LogRow\AbortInvalidIslandLog;
use App\Entity\Log\LogRow\AbortNoShipLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\ForeignIsland\ReinforceBattleshipToForeignIslandPlan;
use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class ReinforceBattleshipPlan extends Plan
{
    public const KEY = 'reinforce_battleship';

    public const NAME = '戦艦派遣';
    public const PRICE = 0;
    public const PRICE_STRING = '(数量x1隻)';
    public const DEFAULT_AMOUNT_STRING = '(無制限)';
    public const AMOUNT_STRING = '(:amount:隻)';
    public const USE_AMOUNT = true;
    public const USE_POINT = false;
    public const USE_TARGET_ISLAND = true;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;
    protected string $amountString = self::AMOUNT_STRING;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $battleships = $terrain->findByTypes([Battleship::TYPE])->filter(function ($cell) use ($island) {
            /** @var CombatantShip $cell */
            return $cell->getAffiliationId() === $island->id;
        });

        if ($this->amount === 0) {
            $this->amount = PHP_INT_MAX;
        }

        if ($battleships->isEmpty()) {
            $logs->add(new AbortNoShipLog($island, $this, new Battleship(point: new Point(0,0))));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $this->amount = min($battleships->count(), $this->amount);

        // 対象が自島の場合は中止とする
        if ($this->getTargetIsland() === $island->id) {
            $logs->add(new AbortInvalidIslandLog($island, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $foreignIslandTargetedPlans->add(new ReinforceBattleshipToForeignIslandPlan(
            $island->id,
            $this->getTargetIsland(),
            deep_copy($this),
        ));

        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
    }
}
