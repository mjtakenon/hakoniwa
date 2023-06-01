<?php

namespace App\Entity\Achievement;

use App\Entity\Status\Status;
use App\Models\Island;
use App\Models\IslandAchievement;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Achievements
{
    protected Collection $achievements;

    public const ACHIEVEMENTS = [
        TurnPrize::TYPE => TurnPrize::class,
        CalamityPrize::TYPE => CalamityPrize::class,
        ProsperityPrize::TYPE => ProsperityPrize::class,
    ];

    public static function create(): static
    {
        return new static();
    }

    /**
     * @param Collection<IslandAchievement> $islandAchievements
     * @return $this
     */
    public function fromModel(Collection $islandAchievements): static
    {
        $this->achievements = new Collection();

        /** @var IslandAchievement $islandAchievement */
        foreach ($islandAchievements as $islandAchievement) {
            /** @var Achievement $class */
            $class = self::ACHIEVEMENTS[$islandAchievement->type];
            $this->achievements->add($class::fromModel($islandAchievement));
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

    public function add(Achievement $achievement): static
    {
        $duplicateAchievements = $this->achievements->filter(function ($existAchievement) use ($achievement){
            /** @var Achievement $existAchievement */
            return $existAchievement->isDuplicate($achievement);
        });

        if ($duplicateAchievements->isNotEmpty()) {
            return $this;
        }

        $this->achievements->add($achievement);
        return $this;
    }

    public function receiveStatusPrize(Island $island, Status $status, Status $prevStatus, Turn $turn): void
    {
        if (ProsperityPrize::isReceivable($status)) {
            $this->add(new ProsperityPrize($island, $turn, null, false));
        }

        if (HighProsperityPrize::isReceivable($status)) {
            $this->add(new HighProsperityPrize($island, $turn, null, false));
        }

        if (SuperProsperityPrize::isReceivable($status)) {
            $this->add(new SuperProsperityPrize($island, $turn, null, false));
        }

        if (CalamityPrize::isReceivable($status, $prevStatus)) {
            $this->add(new CalamityPrize($island, $turn, null, false));
        }

        if (HighCalamityPrize::isReceivable($status, $prevStatus)) {
            $this->add(new HighCalamityPrize($island, $turn, null, false));
        }

        if (SuperCalamityPrize::isReceivable($status, $prevStatus)) {
            $this->add(new SuperCalamityPrize($island, $turn, null, false));
        }
    }

    public function receiveTurnPrize(Island $island, Turn $turn): void
    {
        $this->add(new TurnPrize($island, $turn, ['turn' => $turn->turn], false));
    }
}
