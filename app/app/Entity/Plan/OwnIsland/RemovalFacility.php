<?php

namespace App\Entity\Plan\OwnIsland;

use App\Entity\Achievement\Achievements;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Plain;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Volcano;
use App\Entity\Log\LogRow\AbortInvalidCellLog;
use App\Entity\Log\LogRow\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ExecutePlanResult;
use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class RemovalFacility extends Plan
{
    public const KEY = 'removal_facility';

    public const NAME = '施設の撤去';
    public const PRICE = 0;
    public const PRICE_STRING = '(無料)';

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;

    public function execute(Island $island, Terrain $terrain, Status $status, Achievements $achievements, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);

        if (!in_array($cell::TYPE, self::REMOVABLE_CELLS, true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
        }

        if ($cell->getElevation() === CellConst::ELEVATION_MOUNTAIN) {
            $terrain->setCell($this->point, new Volcano(point: $this->point));
        } else if ($cell->getElevation() === CellConst::ELEVATION_PLAIN) {
            $terrain->setCell($this->point, new Plain(point: $this->point));
        } else if ($cell->getElevation() === CellConst::ELEVATION_SHALLOW) {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        } else {
            $terrain->setCell($this->point, new Sea(point: $this->point));
        }

        $logs = Logs::create()->add(new ExecuteLog($island, $this));

        return new ExecutePlanResult($terrain, $status, $logs, $achievements, false);
    }
}
