<?php

namespace App\Entity\Achievement\Prize;

use App\Entity\Achievement\Achievement;
use App\Entity\Status\Status;

class HighCalamityPrize extends Achievement
{
    public const TYPE = 'high_calamity_prize';
    public const RECEIVABLE_POPULATION = 100000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status, Status $prevStatus): bool
    {
        return ($prevStatus->getPopulation() - $status->getPopulation()) >= self::RECEIVABLE_POPULATION;
    }
}
