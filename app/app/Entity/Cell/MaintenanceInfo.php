<?php

namespace App\Entity\Cell;

class MaintenanceInfo
{
    private int $affiliationId;
    private int $maintenanceNumberOfPeople;

    public function __construct(int $affiliationId, int $maintenanceNumberOfPeople)
    {
        $this->affiliationId = $affiliationId;
        $this->maintenanceNumberOfPeople = $maintenanceNumberOfPeople;
    }

    public function getAffiliationId(): int
    {
        return $this->affiliationId;
    }

    public function getMaintenanceNumberOfPeople(): int
    {
        return $this->maintenanceNumberOfPeople;
    }

}
