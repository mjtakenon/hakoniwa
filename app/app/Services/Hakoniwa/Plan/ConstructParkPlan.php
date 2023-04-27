<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\MonumentOfAgriculture;
use App\Services\Hakoniwa\Cell\Park;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class ConstructParkPlan extends Plan
{
    public const KEY = 'construct_park';

    public const NAME = '公園整備';
    public const PRICE = 3000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = true;

    public const PARKS = [
        MonumentOfAgriculture::class,
        Park::class,
    ];

    public function __construct(Point $point, int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (!in_array($cell::TYPE, self::CONSTRUCTABLE_CELLS, true)) {
            $logs->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        /** @var Park $park */
        foreach (self::PARKS as $park) {
            if ($park::canBuild($terrain, $status)) {
                $terrain->setCell($this->point, new $park(point: $this->point));
                break;
            }
        }

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $turn, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
