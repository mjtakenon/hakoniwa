<?php

namespace App\Entity\Achievement;

use App\Models\Island;
use App\Models\IslandAchievement;
use App\Models\Turn;

abstract class Achievement
{
    protected Island $island;
    protected Turn $turn;
    protected ?array $extra;
    protected bool $isSaved;

    public function __construct(Island $island, Turn $turn, ?array $extra = null, bool $isSaved = true)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->extra = $extra;
        $this->isSaved = $isSaved;
    }

    public static function fromModel(IslandAchievement $islandAchievement): static
    {
        return new static($islandAchievement->island, $islandAchievement->turn, json_decode($islandAchievement->extra, true), true);
    }

    public function getModel(): IslandAchievement
    {
        $islandAchievement = new IslandAchievement();
        $islandAchievement->island_id = $this->island->id;
        $islandAchievement->turn_id = $this->turn->id;
        $islandAchievement->type = $this->getType();
        $islandAchievement->extra = json_encode($this->extra);
        return $islandAchievement;
    }

    abstract public function getType(): string;
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    public function isDuplicate(Achievement $achievement): bool
    {
        return $this->getType() === $achievement->getType() && $this->getExtra() === $achievement->getExtra();
    }
}
