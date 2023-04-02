<?php

namespace App\Services\Hakoniwa\Cell;

class CellTypeConst
{
    const CELL_TYPE_LIST = [
        'sea' => Sea::class,
    ];

    static public function getClassByType(string $type)
    {
        return self::CELL_TYPE_LIST[$type];
    }
}
