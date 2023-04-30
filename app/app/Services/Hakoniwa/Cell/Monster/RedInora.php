<?php

namespace App\Services\Hakoniwa\Cell\Monster;

class RedInora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster1.gif';
    public const TYPE = 'red_inora';
    public const NAME = '怪獣レッドいのら';
    public const DEFAULT_HIT_POINTS = 3;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 8;
    public const CORPSE_PRICE = 3000;

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
        return MonsterConst::APPEARANCE_POPULATION_LV2;
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
