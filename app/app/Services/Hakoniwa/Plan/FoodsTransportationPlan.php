<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\MissileBase\IMissileFireable;
use App\Services\Hakoniwa\Cell\MissileBase\MissileBase;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Cell\OutOfRegion;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Ship\TransportShip;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortInvalidTargetLog;
use App\Services\Hakoniwa\Log\AbortLackOfFoodsLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoMissileBaseLog;
use App\Services\Hakoniwa\Log\AbortNoTransportShipLog;
use App\Services\Hakoniwa\Log\AbortTargetSelfIslandLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\MissileDisabledToMonsterLog;
use App\Services\Hakoniwa\Log\MissileFiringLog;
use App\Services\Hakoniwa\Log\MissileHitToMonsterLog;
use App\Services\Hakoniwa\Log\MissileOutOfRegionLog;
use App\Services\Hakoniwa\Log\MissileSelfDestructLog;
use App\Services\Hakoniwa\Log\SoldMonsterCorpseLog;
use App\Services\Hakoniwa\Plan\ForeignIsland\FiringMissileToForeignIslandPlan;
use App\Services\Hakoniwa\Plan\ForeignIsland\FoodsTransportToForeignIslandPlan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class FoodsTransportationPlan extends Plan
{
    public const KEY = 'foods_transportation';

    public const NAME = '食料輸送';
    public const PRICE = 0;
    public const PRICE_STRING = '(数量x10000 ㌧)';
    public const USE_AMOUNT = true;
    public const USE_TARGET_ISLAND = true;
    public const USE_POINT = false;
    public const UNIT = 10000;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $useAmount = self::USE_AMOUNT;
    protected bool $usePoint = self::USE_POINT;
    protected bool $useTargetIsland = self::USE_TARGET_ISLAND;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $logs = Logs::create();

        $transportShips = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return $cell::TYPE === TransportShip::TYPE;
        });

        if ($this->amount === 0) {
            $this->amount = 100;
        }

        if ($transportShips->isEmpty()) {
            $logs->add(new AbortNoTransportShipLog($island, $turn, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getFoods() < self::UNIT * $this->amount) {
            $logs->add(new AbortLackOfFoodsLog($island, $turn, $this->point, $this));
            $this->amount = 0;
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        // 対象が自島でない場合、後で処理する
        if ($this->getTargetIsland() === $island->id) {
            $logs->add(new AbortTargetSelfIslandLog($island, $turn, $this));
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
