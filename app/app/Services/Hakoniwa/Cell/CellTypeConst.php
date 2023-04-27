<?php

namespace App\Services\Hakoniwa\Cell;

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
    ];

    const IS_LAND = 'is_land';
    const HAS_POPULATION = 'has_population';
    const DESTRUCTIBLE_BY_FIRE = 'destructible_by_fire';
    const DESTRUCTIBLE_BY_TSUNAMI = 'destructible_by_tsunami';
    const DESTRUCTIBLE_BY_EARTHQUAKE = 'destructible_by_earthquake';
    const DESTRUCTIBLE_BY_TYPHOON = 'destructible_by_typhoon';
    const DESTRUCTIBLE_BY_METEORITE = 'destructible_by_meteorite';
    const DESTRUCTIBLE_BY_HUGE_METEORITE = 'destructible_by_huge_meteorite';
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
