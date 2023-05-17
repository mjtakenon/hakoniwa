<?php

namespace App\Entity\Event\ForeignIsland;

use App\Entity\Cell\Cell;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

abstract class ForeignIslandEvent
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

    public abstract function execute(Island $fromIsland, ?Island $toIsland, Terrain $fromTerrain, ?Terrain $toTerrain, Status $fromStatus, ?Status $toStatus, Turn $turn): ForeignIslandEventResult;

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
