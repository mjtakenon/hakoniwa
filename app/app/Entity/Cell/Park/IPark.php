<?php

namespace App\Entity\Cell\Park;

use App\Entity\Achievement\Achievements;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;

interface IPark
{
    public static function canBuild(Terrain $terrain, Status $status, Achievements $achievements): bool;
}
