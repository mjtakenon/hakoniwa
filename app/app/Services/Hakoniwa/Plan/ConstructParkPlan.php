<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Park\MonumentOfAgriculture;
use App\Services\Hakoniwa\Cell\Park\MonumentOfMaster;
use App\Services\Hakoniwa\Cell\Park\MonumentOfMining;
use App\Services\Hakoniwa\Cell\Park\MonumentOfPeace;
use App\Services\Hakoniwa\Cell\Park\MonumentOfWar;
use App\Services\Hakoniwa\Cell\Park\MonumentOfWinner;
use App\Services\Hakoniwa\Cell\Park\Park;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\AbortLackOfFundsLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
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
