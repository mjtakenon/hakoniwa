<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class DisasterResult
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
     * @return Terrain
     */
    public function getTerrain(): Terrain
    {
        return $this->terrain;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getLogs(): Logs
    {
        return $this->logs;
    }
}
