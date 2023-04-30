<?php

namespace App\Services\Hakoniwa\Cell\Monster;

class KingInora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster3.gif';
    public const TYPE = 'king_inora';
    public const NAME = '怪獣キングいのら';
    public const DEFAULT_HIT_POINTS = 6;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 30;
    public const CORPSE_PRICE = 10000;

    public function getName(): string
    {
        return self::NAME;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getImagePath(): string
    {
        return self::IMAGE_PATH;
    }

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
