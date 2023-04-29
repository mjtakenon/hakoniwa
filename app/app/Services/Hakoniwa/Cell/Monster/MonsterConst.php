<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;

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

    public static function getAppearableMonsters(int $population): Collection {
        $appearableMonsters = new Collection();

        if ($population >= self::APPEARANCE_POPULATION_LV4) {
            $appearableMonsters->merge(self::MONSTERS_LIST_LV4);
        }

        if ($population >= self::APPEARANCE_POPULATION_LV3) {
            $appearableMonsters->merge(self::MONSTERS_LIST_LV3);
        }

        if ($population >= self::APPEARANCE_POPULATION_LV2) {
            $appearableMonsters->merge(self::MONSTERS_LIST_LV2);
        }

        if ($population >= self::APPEARANCE_POPULATION_LV1) {
            $appearableMonsters->merge(self::MONSTERS_LIST_LV1);
        }

        return $appearableMonsters;
    }
}
