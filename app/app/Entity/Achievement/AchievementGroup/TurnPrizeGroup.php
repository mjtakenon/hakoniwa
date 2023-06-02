<?php

namespace App\Entity\Achievement\AchievementGroup;

use App\Entity\Achievement\AchievementGroup;
use App\Entity\Achievement\Prize\TurnPrize;

class TurnPrizeGroup extends AchievementGroup
{
    public function getType(): string
    {
        /** @var TurnPrize $achievement */
        $achievement = $this->achievements->first();
        return $achievement->getType();
    }

    public function getHoverText(): ?string
    {
        $turns = $this->achievements->map(function ($achievement) {
            /** @var TurnPrize $achievement */
            return $achievement->getExtra()['turn'];
        })->sort()->toArray();
        return 'turn: ' . implode(',', $turns);
    }

    public function getExtraText(): ?string
    {
        return 'x' . $this->achievements->count();
    }
}
