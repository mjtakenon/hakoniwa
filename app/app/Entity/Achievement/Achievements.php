<?php

namespace App\Entity\Achievement;

use App\Models\IslandAchievement;
use Illuminate\Support\Collection;

class Achievements
{
    protected Collection $achievements;

    public const ACHIEVEMENTS = [
        TurnPrize::TYPE => TurnPrize::class,
        CalamityPrize::TYPE => CalamityPrize::class,
        ProsperityPrize::TYPE => ProsperityPrize::class,
    ];

    public function fromModel(Collection $islandAchievements): static
    {
        $this->achievements = new Collection();

        /** @var IslandAchievement $islandAchievement */
        foreach ($islandAchievements as $islandAchievement) {
            $this->achievements->add((new self::ACHIEVEMENTS[$islandAchievement->type])::fromModel($islandAchievement));
        }

        return $this;
    }

    public function getUnsavedAchievements(): Collection
    {
        return $this->achievements->filter(function ($achievement) {
            /** @var Achievement $achievement */
            return !$achievement->isSaved();
        });
    }

    public function add(Achievement $achievement): void
    {
        $this->achievements->filter(function ($existAchievement) use ($achievement){
            /** @var Achievement $existAchievement */
            return !$existAchievement->isDuplicate($achievement);
        });

        $this->achievements->add($achievement);
    }
}
