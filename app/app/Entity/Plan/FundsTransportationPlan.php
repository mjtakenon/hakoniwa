<?php

namespace App\Entity\Plan;

use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Ship\TransportShip;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\AbortNoShipLog;
use App\Entity\Log\AbortTargetSelfIslandLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ForeignIsland\FundsTransportToForeignIslandPlan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class FundsTransportationPlan extends Plan
{
    public const KEY = 'funds_transportation';

    public const NAME = '送金';
    public const PRICE = 0;
    public const PRICE_STRING = '(数量x100億円)';
    public const DEFAULT_AMOUNT_STRING = '(10000億円)';
    public const AMOUNT_STRING = '(:amount:00億円)';
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const USE_POINT = false;
    public const UNIT = 100;

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

        if ($status->getFunds() < self::UNIT * $this->amount) {
            $logs->add(new AbortLackOfFundsLog($island, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        // 対象が自島でない場合、後で処理する
        if ($this->getTargetIsland() === $island->id) {
            $logs->add(new AbortTargetSelfIslandLog($island, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $foreignIslandTargetedPlans->add(new FundsTransportToForeignIslandPlan(
            $island->id,
            $this->getTargetIsland(),
            deep_copy($this),
        ));

        /** @var TransportShip $transportShip */
        $transportShip = $transportShips->random();
        if ($transportShip->getElevation() === CellConst::ELEVATION_SHALLOW) {
            $terrain->setCell($transportShip->getPoint(), new Shallow(point: $transportShip->getPoint()));
        } else {
            $terrain->setCell($transportShip->getPoint(), new Sea(point: $transportShip->getPoint()));
        }

        $status->setFunds($status->getFunds() - (self::UNIT * $this->amount));

        $this->amount = 0;
        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
