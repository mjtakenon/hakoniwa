<?php

namespace App\Entity\Achievement;

use App\Entity\Status\Status;

class CalamityPrize extends Achievement
{
    public const TYPE = 'calamity_prize';
    public const RECEIVABLE_POPULATION = 50000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status, Status $prevStatus): bool
    {
        return ($prevStatus->getPopulation() - $status->getPopulation()) >= self::RECEIVABLE_POPULATION;
    }
}
