<?php

namespace App\Entity\Achievement;

use App\Models\Turn;

class TurnPrize extends Achievement
{
    public const TYPE = 'turn_prize';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getName(): string
    {
        return $this->turn->turn . 'ターン賞';
    }
}
