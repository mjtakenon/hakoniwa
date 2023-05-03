<?php

namespace App\Services\Hakoniwa\Cell\Park;

use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

interface IPark
{
    public static function canBuild(Terrain $terrain, Status $status): bool;
}
