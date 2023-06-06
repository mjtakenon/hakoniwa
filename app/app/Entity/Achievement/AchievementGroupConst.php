<?php

namespace App\Entity\Achievement;

use App\Entity\Achievement\AchievementGroup\TurnPrizeGroup;
use App\Entity\Achievement\Prize\TurnPrize;
use Illuminate\Support\Collection;

class AchievementGroupConst
{
    public static function getClassByType(string $type, Collection $achievements): ?AchievementGroup
    {
        return match ($type) {
            TurnPrize::TYPE => new TurnPrizeGroup($achievements),
            default => null,
        };
    }
}
