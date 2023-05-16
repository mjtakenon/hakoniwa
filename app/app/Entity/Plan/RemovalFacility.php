<?php

namespace App\Entity\Plan;

use App\Entity\Cell\Plain;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Cell\Volcano;
use App\Entity\Log\AbortInvalidCellLog;
use App\Entity\Log\ExecuteCellLog;
use App\Entity\Log\Logs;
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

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);

        if (!in_array($cell::TYPE, self::REMOVABLE_CELLS, true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $this->point, $this, $cell));
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

        $logs = Logs::create()->add(new ExecuteCellLog($island, $this->point, $this));

        return new ExecutePlanResult($terrain, $status, $logs, false);
    }
}
