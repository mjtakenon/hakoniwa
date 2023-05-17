<?php

namespace App\Entity\Cell;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;

class PassTurnResult
{
    private Terrain $terrain;
    private Status $status;
    private Logs $logs;

    public function __construct(Terrain $terrain, Status $status, Logs $logs)
    {
        $this->terrain = $terrain;
        $this->status = $status;
        $this->logs = $logs;
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

    /**
     * @return Logs
     */
    public function getLogs(): Logs
    {
        return $this->logs;
    }
}
