<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

interface IPark extends ICell
{
    public static function canBuild(Terrain $terrain, Status $status): bool;
}
