<?php

namespace App\Entity\Plan;

use App\Entity\Log\AbandonmentLog;
use App\Entity\Log\ExecuteLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\Turn;
use Illuminate\Support\Collection;

class AbandonmentPlan extends Plan
{
    public const KEY = 'abandonment';

    public const NAME = '島の放棄';
    public const PRICE = 0;
    public const PRICE_STRING = '(無料)';
    public const USE_POINT = false;

    protected string $key = self::KEY;
    protected string $name = self::NAME;
    protected int $price = self::PRICE;
    protected bool $usePoint = self::USE_POINT;

    public function execute(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandTargetedPlans): ExecutePlanResult
    {
        $island->deleted_at = now();

        IslandHistory::createFromIsland($island);
        $logs = Logs::create();
        $logs->add(new ExecuteLog($island, $this));
        $logs->add(new AbandonmentLog($island));

        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
