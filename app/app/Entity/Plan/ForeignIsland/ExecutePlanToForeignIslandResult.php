<?php

namespace App\Entity\Plan\ForeignIsland;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;

class ExecutePlanToForeignIslandResult
{
    private Terrain $fromTerrain;
    private Terrain $toTerrain;
    private Status $fromStatus;
    private Status $toStatus;
    private Logs $fromLogs;
    private Logs $toLogs;

    public function __construct(Terrain $fromTerrain, Terrain $toTerrain, Status $fromStatus, Status $toStatus, Logs $fromLogs, Logs $toLogs)
    {
        $this->fromTerrain = $fromTerrain;
        $this->toTerrain = $toTerrain;
        $this->fromStatus = $fromStatus;
        $this->toStatus = $toStatus;
        $this->fromLogs = $fromLogs;
        $this->toLogs = $toLogs;
    }

    public function getFromStatus(): Status
    {
        return $this->fromStatus;
    }

    public function getToStatus(): Status
    {
        return $this->toStatus;
    }

    public function getFromTerrain(): Terrain
    {
        return $this->fromTerrain;
    }

    public function getToTerrain(): Terrain
    {
        return $this->toTerrain;
    }

    public function getFromLogs(): Logs
    {
        return $this->fromLogs;
    }

    public function getToLogs(): Logs
    {
        return $this->toLogs;
    }
}
