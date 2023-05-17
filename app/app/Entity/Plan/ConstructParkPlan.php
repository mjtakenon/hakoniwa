<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Park\MonumentOfAgriculture;
use App\Entity\Cell\Park\MonumentOfMaster;
use App\Entity\Cell\Park\MonumentOfMining;
use App\Entity\Cell\Park\MonumentOfPeace;
use App\Entity\Cell\Park\MonumentOfWar;
use App\Entity\Cell\Park\MonumentOfWinner;
use App\Entity\Cell\Park\Park;
use App\Entity\Log\AbortInvalidCellLog;
use App\Entity\Log\AbortLackOfFundsLog;
use App\Entity\Log\ExecuteCellLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class ConstructParkPlan extends Plan
{
    public const KEY = 'construct_park';

    public const NAME = '公園整備';
    public const PRICE = 3000;
    public const PRICE_STRING = '(' . self::PRICE . '億円)';

    public const PARKS = [
        MonumentOfAgriculture::class,
        MonumentOfMining::class,
        MonumentOfMaster::class,
        MonumentOfPeace::class,
        MonumentOfWar::class,
        MonumentOfWinner::class,
        Park::class,
    ];

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

        /** @var Park $park */
        foreach (self::PARKS as $park) {
            if ($park::canBuild($terrain, $status)) {
                $terrain->setCell($this->point, new $park(point: $this->point));
                break;
            }
        }

        $status->setFunds($status->getFunds() - self::PRICE);
        $logs->add(new ExecuteCellLog($island, $this->point, $this));
        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
