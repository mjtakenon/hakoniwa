<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Log\AbortLackOfFoodsLog;
use App\Services\Hakoniwa\Log\AbortNoShipLog;
use App\Services\Hakoniwa\Log\AbortTargetSelfIslandLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Plan\ForeignIsland\Plan\FoodsTransportToForeignIslandPlan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class FoodsTransportationPlan extends Plan
{
    public const KEY = 'foods_transportation';

    public const NAME = '食料輸送';
    public const PRICE = 0;
    public const PRICE_STRING = '(数量x10000㌧)';
    public const DEFAULT_AMOUNT_STRING = '(1000000㌧)';
    public const AMOUNT_STRING = '(:amount:0000㌧)';
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const USE_POINT = false;
    public const UNIT = 10000;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected string $defaultAmountString = self::DEFAULT_AMOUNT_STRING;
    protected string $amountString = self::AMOUNT_STRING;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $transportShips = $terrain->findByTypes([TransportShip::TYPE]);

        if ($this->amount === 0) {
            $this->amount = 100;
        }

        if ($transportShips->isEmpty()) {
            $logs->add(new AbortNoShipLog($island, $this, new TransportShip(point: new Point(0,0))));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getFoods() < self::UNIT * $this->amount) {
            $logs->add(new AbortLackOfFoodsLog($island, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        // 対象が自島でない場合、後で処理する
        if ($this->getTargetIsland() === $island->id) {
            $logs->add(new AbortTargetSelfIslandLog($island, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $foreignIslandTargetedPlans->add(new FoodsTransportToForeignIslandPlan(
            $island->id,
            $this->getTargetIsland(),
            deep_copy($this),
        ));

        /** @var TransportShip $transportShip */
        $transportShip = $transportShips->random();
        if ($transportShip->getElevation() === -1) {
            $terrain->setCell($transportShip->getPoint(), new Shallow(point: $transportShip->getPoint()));
        } else {
            $terrain->setCell($transportShip->getPoint(), new Sea(point: $transportShip->getPoint()));
        }

        $status->setFoods($status->getFoods() - (self::UNIT * $this->amount));

        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
