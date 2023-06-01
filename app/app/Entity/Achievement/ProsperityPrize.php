<?php

namespace App\Entity\Achievement;

use App\Entity\Status\Status;

class ProsperityPrize extends Achievement
{
    public const TYPE = 'prosperity_prize';
    public const RECEIVABLE_POPULATION = 300000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status): bool
    {
        return $status->getPopulation() >= self::RECEIVABLE_POPULATION;
    }
}
