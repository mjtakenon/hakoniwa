<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class MonsterConst
{
    public const APPEARANCE_POPULATION_LV1 = 120000;
    public const APPEARANCE_POPULATION_LV2 = 240000;
    public const APPEARANCE_POPULATION_LV3 = 360000;
    public const APPEARANCE_POPULATION_LV4 = 480000;

    public const MONSTERS_LIST_LV1 = [
        Inora::class,
    ];

    public const MONSTERS_LIST_LV2 = [];
    public const MONSTERS_LIST_LV3 = [];
    public const MONSTERS_LIST_LV4 = [];

    public static function getAppearableMonsters(int $population): array {
        if ($population >= self::APPEARANCE_POPULATION_LV4) {
            return array_merge(
                self::MONSTERS_LIST_LV1,
                self::MONSTERS_LIST_LV2,
                self::MONSTERS_LIST_LV3,
                self::MONSTERS_LIST_LV4,
            );
        }

        if ($population >= self::APPEARANCE_POPULATION_LV3) {
            return array_merge(
                self::MONSTERS_LIST_LV1,
                self::MONSTERS_LIST_LV2,
                self::MONSTERS_LIST_LV3,
            );
        }

        if ($population >= self::APPEARANCE_POPULATION_LV2) {
            return array_merge(
                self::MONSTERS_LIST_LV1,
                self::MONSTERS_LIST_LV2,
            );
        }

        if ($population >= self::APPEARANCE_POPULATION_LV2) {
            return self::MONSTERS_LIST_LV1;
        }

        return [];
    }
}
