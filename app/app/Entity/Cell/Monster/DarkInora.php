<?php

namespace App\Entity\Cell\Monster;

class DarkInora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster2.gif';
    public const TYPE = 'dark_inora';
    public const NAME = '怪獣ダークいのら';
    public const DEFAULT_HIT_POINTS = 2;
    public const DEFAULT_MOVE_TIMES = 2;
    public const EXPERIENCE = 8;
    public const CORPSE_PRICE = 3000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

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
