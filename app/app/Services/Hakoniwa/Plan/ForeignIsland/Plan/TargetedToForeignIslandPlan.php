<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland\Plan;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

abstract class TargetedToForeignIslandPlan
{
    protected int $fromIsland;
    protected int $toIsland;
    protected Plan $plan;

    public function __construct(int $fromIsland, int $toIsland, Plan $plan)
    {
        $this->fromIsland = $fromIsland;
        $this->toIsland = $toIsland;
        $this->plan = $plan;
    }

    public abstract function execute(Island $fromIsland, Island $toIsland, Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult;

    /**
     * @return int
     */
    public function getFromIsland(): int
    {
        return $this->fromIsland;
    }

    /**
     * @return int
     */
    public function getToIsland(): int
    {
        return $this->toIsland;
    }

    /**
     * @return Plan
     */
    public function getPlan(): Plan
    {
        return $this->plan;
    }
}
