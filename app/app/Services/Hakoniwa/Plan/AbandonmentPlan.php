<?php

namespace App\Services\Hakoniwa\Plan;

use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\AbandonmentLog;
use App\Services\Hakoniwa\Log\ExecuteLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
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
        $logs->add(new ExecuteLog($island, $turn, $this));
        $logs->add(new AbandonmentLog($island, $turn));

        return new ExecutePlanResult($terrain, $status, $logs, true);
    }
}
