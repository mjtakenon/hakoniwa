<?php

namespace App\Entity\Plan;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;

class ExecutePlanResult
{
    private Terrain $terrain;
    private Status $status;
    private Logs $logs;
    private bool $isTurnSpending;

    public function __construct(Terrain $terrain, Status $status, Logs $logs, bool $isTurnSpending)
    {
        $this->terrain = $terrain;
        $this->status = $status;
        $this->logs = $logs;
        $this->isTurnSpending = $isTurnSpending;
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

    /**
     * @return bool
     */
    public function isTurnSpending(): bool
    {
        return $this->isTurnSpending;
    }
}
