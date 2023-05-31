<?php

namespace App\Entity\Achievement;

class ProsperityPrize extends Achievement
{
    public const TYPE = 'prosperity_prize';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getName(): string
    {
        return '繁栄賞';
    }
}
