<?php

namespace App\Services\Hakoniwa\Plan\ForeignIsland\Event;

use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class ForeignIslandOccurEventResult
{
    private Terrain $fromTerrain;
    private ?Terrain $toTerrain;
    private Status $fromStatus;
    private ?Status $toStatus;
    private Logs $fromLogs;
    private Logs $toLogs;

    public function __construct(Terrain $fromTerrain, ?Terrain $toTerrain, Status $fromStatus, ?Status $toStatus, Logs $fromLogs, ?Logs $toLogs)
    {
        $this->fromTerrain = $fromTerrain;
        $this->toTerrain = $toTerrain;
        $this->fromStatus = $fromStatus;
        $this->toStatus = $toStatus;
        $this->fromLogs = $fromLogs;
        $this->toLogs = $toLogs;
    }

    /**
     * @return Status
     */
    public function getFromStatus(): Status
    {
        return $this->fromStatus;
    }

    /**
     * @return Status|null
     */
    public function getToStatus(): ?Status
    {
        return $this->toStatus;
    }

    /**
     * @return Terrain
     */
    public function getFromTerrain(): Terrain
    {
        return $this->fromTerrain;
    }

    /**
     * @return Terrain|null
     */
    public function getToTerrain(): ?Terrain
    {
        return $this->toTerrain;
    }

    /**
     * @return Logs
     */
    public function getFromLogs(): Logs
    {
        return $this->fromLogs;
    }

    /**
     * @return Logs|null
     */
    public function getToLogs(): ?Logs
    {
        return $this->toLogs;
    }
}
