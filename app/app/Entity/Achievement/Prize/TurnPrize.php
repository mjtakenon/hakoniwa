<?php

namespace App\Entity\Achievement\Prize;

use App\Entity\Achievement\Achievement;

class TurnPrize extends Achievement
{
    public const TYPE = 'turn_prize';

    public function getType(): string
    {
        return self::TYPE;
    }
}
