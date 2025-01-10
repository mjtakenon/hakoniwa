<?php

namespace App\Entity\Cell\Others;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;

class Sea extends Cell
{
    public const TYPE = 'sea';
    public const NAME = '海';
    const ATTRIBUTE = [
        CellConst::IS_LAND => false,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::IS_MOUNTAIN => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => false,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => false,
    ];
    public const ELEVATION = CellConst::ELEVATION_SEA;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $elevation = self::ELEVATION;
}
