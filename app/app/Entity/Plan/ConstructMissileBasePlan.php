<?php

namespace App\Entity\Plan;

use App\Entity\Cell\MissileFireable\MissileBase;
use App\Entity\Log\AbortInvalidCellLog;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\AfforestationLog;
use App\Entity\Log\ExecuteCellLog;
use App\Entity\Log\Logs;
use App\Entity\Log\LogVisibility;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class ConstructMissileBasePlan extends Plan
{
    public const KEY = 'construct_missile_base';

    public const NAME = 'ミサイル基地建設';
    public const PRICE = 300;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

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

        if (!in_array($cell::TYPE, self::CONSTRUCTABLE_CELLS, true)) {
            $logs->add(new AbortInvalidCellLog($island, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $terrain->setCell($this->point, new MissileBase(point: $this->point));
        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $this->point, $this, LogVisibility::VISIBILITY_PRIVATE));
        $logs->add(new AfforestationLog($island));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
