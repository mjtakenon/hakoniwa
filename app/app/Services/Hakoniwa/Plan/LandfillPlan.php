<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Lake;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\AbortNoLandsLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class LandfillPlan extends Plan
{
    public const KEY = 'landfill';

    public const NAME = '埋め立て';
    public const PRICE = 150;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = true;

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
        if ($status->getFunds() < self::PRICE) {
            $logs = Logs::create()->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (!in_array($cell::TYPE, [Shallow::TYPE, Sea::TYPE, Lake::TYPE], true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $landCells = $terrain->getAroundCells($cell->getPoint())->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::IS_LAND];
        });

        if ($landCells->count() === 0) {
            $logs = Logs::create()->add(new AbortNoLandsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (in_array($cell::TYPE, [Shallow::TYPE, Lake::TYPE], true)) {
            $terrain->setCell($this->point, new Wasteland(point: $this->point));

            // 周囲が3セル以上陸地だった場合、周囲の海は浅瀬になる
            $cells = $terrain->getAroundCells($this->point);
            $aroundGroundCount = 0;
            /** @var Cell $c */
            foreach ($cells as $c) {
                if ($c::ATTRIBUTE[CellTypeConst::IS_LAND]){
                    $aroundGroundCount += 1;
                }
            }
            if ($aroundGroundCount >= 3) {
                foreach ($cells as $c) {
                    if ($c::TYPE === Sea::TYPE){
                        $terrain->setCell($c->getPoint(), new Shallow(point: $c->getPoint()));
                    }
                }
            }
        } else {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        }

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteCellLog($island, $turn, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
