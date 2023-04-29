<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

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
     * @return array
     */
    public function getLogs(): Logs
    {
        return $this->logs;
    }
}
