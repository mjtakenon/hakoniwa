<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

interface IMissileFireable extends ICell
{
    public function getLevel(): int;
    public function setExperience(int $experience);
    public function getExperience(): int;
}
