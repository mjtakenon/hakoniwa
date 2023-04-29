<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

interface IMissileFireable
{
    public function getLevel(): int;
    public function setExperience(int $experience);
    public function getExperience(int $experience): int;
}
