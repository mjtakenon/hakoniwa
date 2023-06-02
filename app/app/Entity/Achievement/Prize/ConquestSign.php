<?php

namespace App\Entity\Achievement\Prize;

use App\Entity\Achievement\Achievement;
use App\Entity\Status\Status;

class ConquestSign extends Achievement
{
    public const TYPE = 'conquest_sign';
    public const RECEIVABLE_DEVELOPMENT_POINTS = 5000000;

    public function getType(): string
    {
        return self::TYPE;
    }

    public static function isReceivable(Status $status): bool
    {
        return $status->getDevelopmentPoints() >= self::RECEIVABLE_DEVELOPMENT_POINTS;
    }
}
