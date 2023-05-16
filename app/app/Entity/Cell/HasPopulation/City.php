<?php

namespace App\Entity\Cell\HasPopulation;

class City extends HasPopulation
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land5.gif';
    public const TYPE = 'city';
    public const NAME = '都市';

    public const MIN_POPULATION = 10000;
    public const MAX_POPULATION = 20000;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    protected int $minPopulation = self::MIN_POPULATION;
    protected int $maxPopulation = self::MAX_POPULATION;
}
