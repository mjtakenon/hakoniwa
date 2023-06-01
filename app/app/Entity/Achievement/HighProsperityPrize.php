<?php

namespace App\Entity\Achievement;

use App\Entity\Status\Status;

class HighProsperityPrize extends Achievement
{
    public const TYPE = 'high_prosperity_prize';
    public const RECEIVABLE_POPULATION = 500000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status): bool
    {
        return $status->getPopulation() >= self::RECEIVABLE_POPULATION;
    }
}
