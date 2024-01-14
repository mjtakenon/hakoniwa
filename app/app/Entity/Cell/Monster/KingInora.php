<?php

namespace App\Entity\Cell\Monster;

class KingInora extends Monster
{
    public const TYPE = 'king_inora';
    public const NAME = '怪獣キングいのら';
    public const DEFAULT_HIT_POINTS = 6;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 30;
    public const CORPSE_PRICE = 10000;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;


    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV4;
    }

    public function getCorpsePrice(): int
    {
        return self::CORPSE_PRICE;
    }

    public function getExperience(): int
    {
        return self::EXPERIENCE;
    }

    public function getDefaultHitPoints(): int
    {
        return self::DEFAULT_HIT_POINTS;
    }

    public function getDefaultMoveTimes(): int
    {
        return self::DEFAULT_MOVE_TIMES;
    }
}
