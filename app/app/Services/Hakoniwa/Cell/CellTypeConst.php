<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Cell\Monster\DarkInora;
use App\Services\Hakoniwa\Cell\Monster\GhostInora;
use App\Services\Hakoniwa\Cell\Monster\Hamunemu;
use App\Services\Hakoniwa\Cell\Monster\Inora;
use App\Services\Hakoniwa\Cell\Monster\KingInora;
use App\Services\Hakoniwa\Cell\Monster\Kujira;
use App\Services\Hakoniwa\Cell\Monster\RedInora;
use App\Services\Hakoniwa\Cell\Monster\Sanjira;
use App\Services\Hakoniwa\Cell\Monster\Slime;
use App\Services\Hakoniwa\Cell\Monster\SlimeLegend;
use App\Services\Hakoniwa\Cell\Park\MonumentOfAgriculture;
use App\Services\Hakoniwa\Cell\Park\MonumentOfMaster;
use App\Services\Hakoniwa\Cell\Park\MonumentOfMining;
use App\Services\Hakoniwa\Cell\Park\MonumentOfPeace;
use App\Services\Hakoniwa\Cell\Park\MonumentOfWar;
use App\Services\Hakoniwa\Cell\Park\MonumentOfWinner;
use App\Services\Hakoniwa\Cell\Park\Park;

class CellTypeConst
{
    const CELL_TYPE_LIST = [
        City::TYPE => City::class,
        Factory::TYPE => Factory::class,
        Farm::TYPE => Farm::class,
        FarmDome::TYPE => FarmDome::class,
        Forest::TYPE => Forest::class,
        Metropolis::TYPE => Metropolis::class,
        Mountain::TYPE => Mountain::class,
        Mine::TYPE => Mine::class,
        Oilfield::TYPE => Oilfield::class,
        Plain::TYPE => Plain::class,
        Sea::TYPE => Sea::class,
        Shallow::TYPE => Shallow::class,
        Lake::TYPE => Lake::class,
        LargeFactory::TYPE => LargeFactory::class,
        Town::TYPE => Town::class,
        Village::TYPE => Village::class,
        Wasteland::TYPE => Wasteland::class,
        MissileBase::TYPE => MissileBase::class,
        SeabedBase::TYPE => SeabedBase::class,
        Park::TYPE => Park::class,
        MonumentOfAgriculture::TYPE => MonumentOfAgriculture::class,
        MonumentOfMining::TYPE => MonumentOfMining::class,
        MonumentOfMaster::TYPE => MonumentOfMaster::class,
        MonumentOfPeace::TYPE => MonumentOfPeace::class,
        MonumentOfWar::TYPE => MonumentOfWar::class,
        MonumentOfWinner::TYPE => MonumentOfWinner::class,
        Inora::TYPE => Inora::class,
        RedInora::TYPE => RedInora::class,
        DarkInora::TYPE => DarkInora::class,
        KingInora::TYPE => KingInora::class,
        Sanjira::TYPE => Sanjira::class,
        Kujira::TYPE => Kujira::class,
        Hamunemu::TYPE => Hamunemu::class,
        GhostInora::TYPE => GhostInora::class,
        Slime::TYPE => Slime::class,
        SlimeLegend::TYPE => SlimeLegend::class,
    ];

    const IS_LAND = 'is_land';
    const IS_MONSTER = 'is_monster';
    const HAS_POPULATION = 'has_population';
    const DESTRUCTIBLE_BY_FIRE = 'destructible_by_fire';
    const DESTRUCTIBLE_BY_TSUNAMI = 'destructible_by_tsunami';
    const DESTRUCTIBLE_BY_EARTHQUAKE = 'destructible_by_earthquake';
    const DESTRUCTIBLE_BY_TYPHOON = 'destructible_by_typhoon';
    const DESTRUCTIBLE_BY_METEORITE = 'destructible_by_meteorite';
    const DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX = 'destructible_by_wide_area_damage_2hex';
    const DESTRUCTIBLE_BY_MISSILE = 'destructible_by_missile';
    const DESTRUCTIBLE_BY_RIOT = 'destructible_by_riot';
    const DESTRUCTIBLE_BY_MONSTER = 'destructible_by_monster';
    const PREVENTING_FIRE = 'preventing_fire';
    const PREVENTING_TYPHOON = 'preventing_typhoon';
    const PREVENTING_TSUNAMI = 'preventing_tsunami';

    static public function getClassByType(string $type): string
    {
        return self::CELL_TYPE_LIST[$type];
    }
}
