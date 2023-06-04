<?php

namespace App\Entity\Achievement;

use App\Entity\Achievement\AchievementGroup\TurnPrizeGroup;
use App\Entity\Achievement\Prize\CalamityPrize;
use App\Entity\Achievement\Prize\ConquestSign;
use App\Entity\Achievement\Prize\HighCalamityPrize;
use App\Entity\Achievement\Prize\HighProsperityPrize;
use App\Entity\Achievement\Prize\ProsperityPrize;
use App\Entity\Achievement\Prize\SuperCalamityPrize;
use App\Entity\Achievement\Prize\SuperProsperityPrize;
use App\Entity\Achievement\Prize\TurnPrize;

class AchievementConst
{
    public const ACHIEVEMENTS = [
        TurnPrize::TYPE => TurnPrize::class,
        ConquestSign::TYPE => ConquestSign::class,
        CalamityPrize::TYPE => CalamityPrize::class,
        HighCalamityPrize::TYPE => HighCalamityPrize::class,
        SuperCalamityPrize::TYPE => SuperCalamityPrize::class,
        ProsperityPrize::TYPE => ProsperityPrize::class,
        HighProsperityPrize::TYPE => HighProsperityPrize::class,
        SuperProsperityPrize::TYPE => SuperProsperityPrize::class,
    ];

    public const ACHIEVEMENT_GROUP = [
        TurnPrize::TYPE => TurnPrizeGroup::class,
    ];

    public static function getClassByType(string $type): string
    {
        return self::ACHIEVEMENTS[$type];
    }
}