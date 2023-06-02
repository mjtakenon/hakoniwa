<?php

namespace App\Entity\Achievement;

use App\Entity\Achievement\AchievementGroup\TurnPrizeGroup;
use App\Entity\Achievement\Prize\CalamityPrize;
use App\Entity\Achievement\Prize\HighCalamityPrize;
use App\Entity\Achievement\Prize\HighProsperityPrize;
use App\Entity\Achievement\Prize\ProsperityPrize;
use App\Entity\Achievement\Prize\SuperCalamityPrize;
use App\Entity\Achievement\Prize\SuperProsperityPrize;
use App\Entity\Achievement\Prize\TurnPrize;

class AchievementGroupConst
{
    public const ACHIEVEMENT_GROUP = [
        TurnPrize::TYPE => TurnPrizeGroup::class,
    ];

    public static function getClassByType(string $type): ?string
    {
        if (array_key_exists($type, AchievementConst::ACHIEVEMENT_GROUP)) {
            return self::ACHIEVEMENT_GROUP[$type];
        }

        return null;
    }
}
