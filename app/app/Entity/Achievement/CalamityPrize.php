<?php

namespace App\Entity\Achievement;

class CalamityPrize extends Achievement
{
    public const TYPE = 'calamity_prize';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getName(): string
    {
        return '災難賞';
    }
}
