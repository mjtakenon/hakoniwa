<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class PlanExecuteResult
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
     * @return array
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
