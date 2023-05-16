<?php

namespace App\Entity\Cell\MissileBase;

interface IMissileFireable
{
    public function getLevel(): int;
    public function setExperience(int $experience);
    public function getExperience(): int;
}
