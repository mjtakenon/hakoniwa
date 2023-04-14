<?php

namespace App\Services\Hakoniwa\Disaster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

interface IDisaster
{
    public static function occur(Island $island, Terrain $terrain, Status $status, Turn $turn): DisasterResult;
}
