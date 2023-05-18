<?php

namespace App\Entity\Cell;

use App\Models\Island;

interface IHasMaintenanceNumberOfPeople
{
    public function getMaintenanceNumberOfPeople(Island $island): int;
}
