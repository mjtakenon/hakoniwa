<?php

namespace App\Entity\Plan\ForeignIsland;

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

    public function getFromIsland(): int
    {
        return $this->fromIsland;
    }

    public function getToIsland(): int
    {
        return $this->toIsland;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }
}
