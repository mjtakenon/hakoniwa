<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

abstract class ForeignIslandOccurEvent
{
    protected int $fromIsland;
    protected int $toIsland;
    protected Cell $cell;

    public function __construct(int $fromIsland, int $toIsland, Cell $cell)
    {
        $this->fromIsland = $fromIsland;
        $this->toIsland = $toIsland;
        $this->cell = $cell;
    }

    public abstract function execute(Island $fromIsland, ?Island $toIsland, Terrain $fromTerrain, ?Terrain $toTerrain, Status $fromStatus, ?Status $toStatus, Turn $turn): ExecutePlanToForeignIslandResult;

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
     * @return Cell
     */
    public function getCell(): Cell
    {
        return $this->cell;
    }
}
