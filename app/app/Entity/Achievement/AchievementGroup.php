<?php

namespace App\Entity\Achievement;

use Illuminate\Support\Collection;

abstract class AchievementGroup
{
    /** @var Collection<Achievement> */
    protected Collection $achievements;

    public function __construct(Collection $achievements)
    {
        $this->achievements = $achievements;
    }

    public abstract function getType(): string;

    public abstract function getHoverText(): ?string;

    public abstract function getExtraText(): ?string;
}
