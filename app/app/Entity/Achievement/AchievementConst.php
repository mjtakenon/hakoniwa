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
use App\Models\Island;
use App\Models\IslandAchievement;
use App\Models\Turn;

class AchievementConst
{
    public static function getClassByIslandAchievement(IslandAchievement $islandAchievement): Achievement
    {
        return match ($islandAchievement->type) {
            TurnPrize::TYPE => TurnPrize::fromModel($islandAchievement),
            ConquestSign::TYPE => ConquestSign::fromModel($islandAchievement),
            CalamityPrize::TYPE => CalamityPrize::fromModel($islandAchievement),
            HighCalamityPrize::TYPE => HighCalamityPrize::fromModel($islandAchievement),
            SuperCalamityPrize::TYPE => SuperCalamityPrize::fromModel($islandAchievement),
            ProsperityPrize::TYPE => ProsperityPrize::fromModel($islandAchievement),
            HighProsperityPrize::TYPE => HighProsperityPrize::fromModel($islandAchievement),
            SuperProsperityPrize::TYPE => SuperProsperityPrize::fromModel($islandAchievement),
        };
    }
}
