<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Volcano;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
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

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);

        if (!in_array($cell::TYPE, self::REMOVABLE_CELLS, true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($cell->getElevation() === 1) {
            $terrain->setCell($this->point, new Volcano(point: $this->point));
        } else if ($cell->getElevation() === 0) {
            $terrain->setCell($this->point, new Plain(point: $this->point));
        } else if ($cell->getElevation() === -1) {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        } else {
            $terrain->setCell($this->point, new Sea(point: $this->point));
        }

        $logs = Logs::create()->add(new ExecuteCellLog($island, $turn, $this->point, $this));

        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
