<?php

namespace App\Services\Hakoniwa\Cell;

class CellTypeConst
{
    const CELL_TYPE_LIST = [
        City::TYPE => City::class,
        Factory::TYPE => Factory::class,
        Farm::TYPE => Farm::class,
        Forest::TYPE => Forest::class,
        Mountain::TYPE => Mountain::class,
        Plain::TYPE => Plain::class,
        Sea::TYPE => Sea::class,
        Shallow::TYPE => Shallow::class,
        Town::TYPE => Town::class,
        Village::TYPE => Village::class,
        Wasteland::TYPE => Wasteland::class,
    ];

    static public function getClassByType(string $type)
    {
        return self::CELL_TYPE_LIST[$type];
    }
}
