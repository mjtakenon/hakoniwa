<?php

namespace App\Services\Hakoniwa\Cell;

interface IMissileFireable
{
    public function getLevel(): int;
    public function setExperience(int $experience);
    public function getExperience(): int;
}
