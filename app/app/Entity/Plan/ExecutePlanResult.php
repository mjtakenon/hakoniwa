<?php

namespace App\Entity\Plan;

use App\Entity\Achievement\Achievements;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;

class ExecutePlanResult
{
    private Terrain $terrain;
    private Status $status;
    private Logs $logs;
    private Achievements $achievements;
    private bool $isTurnSpending;

    public function __construct(Terrain $terrain, Status $status, Logs $logs, Achievements $achievements, bool $isTurnSpending)
    {
        $this->terrain = $terrain;
        $this->status = $status;
        $this->logs = $logs;
        $this->achievements = $achievements;
        $this->isTurnSpending = $isTurnSpending;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getTerrain(): Terrain
    {
        return $this->terrain;
    }

    public function getLogs(): Logs
    {
        return $this->logs;
    }

    public function getAchievements(): Achievements
    {
        return $this->achievements;
    }

    public function isTurnSpending(): bool
    {
        return $this->isTurnSpending;
    }
}
