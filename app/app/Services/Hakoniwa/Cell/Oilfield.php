<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Oilfield extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land16.gif';
    public const TYPE = 'oilfield';
    public const NAME = '油田';
    const PRODUCTION_NUMBER_OF_PEOPLE = 20000;
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
        $this->resourcesProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            $this->resourcesProductionNumberOfPeople . '人規模';
    }
}
