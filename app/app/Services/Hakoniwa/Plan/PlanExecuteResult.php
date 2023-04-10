<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class PlanExecuteResult
{
    private Terrain $terrain;
    private Status $status;

    public function __construct(Terrain $terrain, Status $status)
    {
        $this->terrain = $terrain;
        $this->status = $status;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Terrain
     */
    public function getTerrain(): Terrain
    {
        return $this->terrain;
    }
}
