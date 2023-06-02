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

abstract class AchievementConst
{
    public const ACHIEVEMENTS = [
        TurnPrize::TYPE => TurnPrize::class,
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
}
