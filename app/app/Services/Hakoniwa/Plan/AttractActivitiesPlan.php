<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\City;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\ExecuteLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class AttractActivitiesPlan extends Plan
{
    public const KEY = 'attract_activities';

    public const NAME = '誘致活動';
    public const PRICE = 1000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';
    public const USE_POINT = false;
    public const USE_AMOUNT = true;

    public function __construct(Point $point = (new Point(0,0)), int $amount = 1)
    {
        parent::__construct($point, $amount);
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
        $this->usePoint = self::USE_POINT;
        $this->useAmount = self::USE_AMOUNT;
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn): ExecutePlanResult
    {
        $logs = Logs::create();

        if ($status->getFunds() < self::PRICE) {
            $logs->add(new AbortLackOfFundsLog($island, $turn, $this->point, $this));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        /** @var Cell $cell */
        $hasPopulationCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::HAS_POPULATION];
        });

        foreach ($hasPopulationCells as $cell) {
            $addPopulation = random_int(1, 3) * 100;
            $cell->setPopulation($cell->getPopulation() + $addPopulation);
        }

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs = Logs::create()->add(new ExecuteLog($island, $turn, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
