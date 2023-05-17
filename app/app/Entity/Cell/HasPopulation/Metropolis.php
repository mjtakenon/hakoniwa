<?php

namespace App\Entity\Cell\HasPopulation;

class Metropolis extends City
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land502.gif';
    public const TYPE = 'metropolis';
    public const NAME = '大都市';
    public const MIN_POPULATION = 20000;
    public const MAX_POPULATION = 30000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    protected int $minPopulation = self::MIN_POPULATION;
    protected int $maxPopulation = self::MAX_POPULATION;
}
