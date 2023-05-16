<?php

namespace App\Entity\Plan\ForeignIsland\Plan;

use App\Entity\Plan\Plan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

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
