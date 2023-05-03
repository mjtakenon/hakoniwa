<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\AbortInvalidCellLog;
use App\Services\Hakoniwa\Log\ExecuteCellLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

class RemovalFacility extends Plan
{
    public const KEY = 'removal_facility';

    public const NAME = '施設の撤去';
    public const PRICE = 0;
    public const PRICE_STRING = '(無料)';
    public const USE_POINT = true;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $usePoint = self::USE_POINT;

    public function __construct(Point $point = (new Point(0, 0)), int $amount = 1, ?int $targetIsland = null)
    {
        parent::__construct($point, $amount);
    }

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $cell = $terrain->getCell($this->point);

        if (!in_array($cell::TYPE, self::REMOVABLE_CELLS, true)) {
            $logs = Logs::create()->add(new AbortInvalidCellLog($island, $turn, $this->point, $this, $cell));
            return new ExecutePlanResult($terrain, $status, $logs, false);
        }

        if ($cell::ELEVATION === 1) {
            $terrain->setCell($this->point, new Mountain(point: $this->point));
        } else if ($cell::ELEVATION === 0) {
            $terrain->setCell($this->point, new Plain(point: $this->point));
        } else if ($cell::ELEVATION === -1) {
            $terrain->setCell($this->point, new Shallow(point: $this->point));
        } else {
            $terrain->setCell($this->point, new Sea(point: $this->point));
        }

        $logs = Logs::create()->add(new ExecuteCellLog($island, $turn, $this->point, $this));

        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
