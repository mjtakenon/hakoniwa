<?php

namespace App\Entity\Disaster;

use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;

interface IDisaster
{
    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult;
}
