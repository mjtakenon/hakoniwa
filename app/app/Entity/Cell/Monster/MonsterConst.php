<?php

namespace App\Entity\Cell\Monster;

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
    public const MONSTERS_LIST_LV2 = [
        RedInora::class,
        DarkInora::class,
        Sanjira::class,
        Hamunemu::class,
    ];
    public const MONSTERS_LIST_LV3 = [
        Kujira::class,
        GhostInora::class,
        Slime::class,
    ];
    public const MONSTERS_LIST_LV4 = [
        KingInora::class,
        SlimeLegend::class,
    ];

    public static function getAppearableMonsters(int $population): Collection {
        $appearableMonsters = new Collection();

        if ($population >= self::APPEARANCE_POPULATION_LV4) {
            $appearableMonsters = $appearableMonsters->merge(self::MONSTERS_LIST_LV4);
        }

        if ($population >= self::APPEARANCE_POPULATION_LV3) {
            $appearableMonsters = $appearableMonsters->merge(self::MONSTERS_LIST_LV3);
        }

        if ($population >= self::APPEARANCE_POPULATION_LV2) {
            $appearableMonsters = $appearableMonsters->merge(self::MONSTERS_LIST_LV2);
        }

        if ($population >= self::APPEARANCE_POPULATION_LV1) {
            $appearableMonsters = $appearableMonsters->merge(self::MONSTERS_LIST_LV1);
        }

        return $appearableMonsters;
    }
}
