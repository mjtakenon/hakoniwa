<?php

namespace App\Entity\Achievement\Prize;

use App\Entity\Achievement\Achievement;
use App\Entity\Status\Status;

class SuperProsperityPrize extends Achievement
{
    public const TYPE = 'super_prosperity_prize';
    public const RECEIVABLE_POPULATION = 1000000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status): bool
    {
        return $status->getPopulation() >= self::RECEIVABLE_POPULATION;
    }
}
