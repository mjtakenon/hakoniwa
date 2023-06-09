<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Lake;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogRow\AbortInvalidCellLog;
use App\Entity\Log\LogRow\AbortLackOfFundsLog;
use App\Entity\Log\LogRow\AbortNoAdjacentLandsLog;
use App\Entity\Log\LogRow\AbortNoDevelopmentPointsLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
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

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if ($status->getDevelopmentPoints() < self::EXECUTABLE_DEVELOPMENT_POINT) {
            $logs->add(new AbortNoDevelopmentPointsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if (!in_array($cell::TYPE, [Shallow::TYPE, Sea::TYPE, Lake::TYPE], true)) {
            $logs->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        $landCells = $terrain->getAroundCells($cell->getPoint())->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellConst::IS_LAND];
        });

        if ($landCells->count() === 0) {
            $logs->add(new AbortNoAdjacentLandsLog($island, $this));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if (in_array($cell::TYPE, [Shallow::TYPE, Lake::TYPE], true)) {
            $terrain->setCell($this->point, new Wasteland(point: $this->point));

            // 周囲が3セル以上陸地だった場合、周囲の海は浅瀬になる
            $cells = $terrain->getAroundCells($this->point);
            $aroundGroundCount = $cells->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellConst::IS_LAND];
            })->count();
            if ($aroundGroundCount >= 3) {
                foreach ($cells->filter(function ($cell) {
                    return $cell::TYPE === Sea::TYPE;
                }) as $c) {
                    $terrain->setCell($c->getPoint(), new Shallow(point: $c->getPoint()));
                }
            }
        } else {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        }

        $terrain->replaceShallowToLake();

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteLog($island, $this));
        return new ExecutePlanResult($terrain, $status, $logs, $achievements, true);
    }
}
