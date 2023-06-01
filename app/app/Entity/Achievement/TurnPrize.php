<?php

namespace App\Entity\Achievement;

class TurnPrize extends Achievement
{
    public const TYPE = 'turn_prize';

    public function getType(): string
    {
        return self::TYPE;
    }
}
