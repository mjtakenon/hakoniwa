<?php

namespace App\Entity\Achievement\Prize;

use App\Entity\Achievement\Achievement;
use App\Entity\Status\Status;

class SuperCalamityPrize extends Achievement
{
    public const TYPE = 'super_calamity_prize';
    public const RECEIVABLE_POPULATION = 200000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status, Status $prevStatus): bool
    {
        return ($prevStatus->getPopulation() - $status->getPopulation()) >= self::RECEIVABLE_POPULATION;
    }
}
