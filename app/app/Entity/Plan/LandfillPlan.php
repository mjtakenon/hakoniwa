<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Lake;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\AbortInvalidCellLog;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\AbortNoDevelopmentPointsLog;
use App\Entity\Log\AbortNoLandsLog;
use App\Entity\Log\ExecuteCellLog;
use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class LandfillPlan extends Plan
{
    public const KEY = 'landfill';

    public const NAME = '埋め立て';
    public const PRICE = 150;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const EXECUTABLE_DEVELOPMENT_POINT = DevelopmentPointsConst::LANDFILL_AVAILABLE_POINTS;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected int $executableDevelopmentPoint = self::EXECUTABLE_DEVELOPMENT_POINT;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $logs->add(new AbortNoDevelopmentPointsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (!in_array($cell::TYPE, [Shallow::TYPE, Sea::TYPE, Lake::TYPE], true)) {
            $logs->add(new AbortInvalidCellLog($island, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        $landCells = $terrain->getAroundCells($cell->getPoint())->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellConst::IS_LAND];
        });

        if ($landCells->count() === 0) {
            $logs->add(new AbortNoLandsLog($island, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if (in_array($cell::TYPE, [Shallow::TYPE, Lake::TYPE], true)) {
            $terrain->setCell($this->point, new Wasteland(point: $this->point));

            // 周囲が3セル以上陸地だった場合、周囲の海は浅瀬になる
            $cells = $terrain->getAroundCells($this->point);
            $aroundGroundCount = 0;
            /** @var Cell $c */
            foreach ($cells as $c) {
                if ($c::ATTRIBUTE[CellConst::IS_LAND]) {
                    $aroundGroundCount += 1;
                }
            }
            if ($aroundGroundCount >= 3) {
                foreach ($cells as $c) {
                    if ($c::TYPE === Sea::TYPE) {
                        $terrain->setCell($c->getPoint(), new Shallow(point: $c->getPoint()));
                    }
                }
            }
        } else {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        }

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
