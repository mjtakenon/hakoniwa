<?php

namespace App\Entity\Cell\Monster;

class Inora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster0.gif';
    public const TYPE = 'inora';
    public const NAME = '怪獣いのら';
    public const DEFAULT_HIT_POINTS = 1;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 5;
    public const CORPSE_PRICE = 1500;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;


    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV1;
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
