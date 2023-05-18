<?php

namespace App\Entity\Cell;

use App\Entity\Cell\FoodsProduction\Farm;
use App\Entity\Cell\FoodsProduction\FarmDome;
use App\Entity\Cell\FundsProduction\Factory;
use App\Entity\Cell\FundsProduction\LargeFactory;
use App\Entity\Cell\HasPopulation\City;
use App\Entity\Cell\HasPopulation\Metropolis;
use App\Entity\Cell\HasPopulation\Town;
use App\Entity\Cell\HasPopulation\Village;
use App\Entity\Cell\HasWoods\Forest;
use App\Entity\Cell\MissileFireable\MissileBase;
use App\Entity\Cell\MissileFireable\SeabedBase;
use App\Entity\Cell\Monster\DarkInora;
use App\Entity\Cell\Monster\GhostInora;
use App\Entity\Cell\Monster\Hamunemu;
use App\Entity\Cell\Monster\Inora;
use App\Entity\Cell\Monster\KingInora;
use App\Entity\Cell\Monster\Kujira;
use App\Entity\Cell\Monster\RedInora;
use App\Entity\Cell\Monster\Sanjira;
use App\Entity\Cell\Monster\Slime;
use App\Entity\Cell\Monster\SlimeLegend;
use App\Entity\Cell\Others\Lake;
use App\Entity\Cell\Others\Mountain;
use App\Entity\Cell\Others\Plain;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Volcano;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\Park\MonumentOfAgriculture;
use App\Entity\Cell\Park\MonumentOfMaster;
use App\Entity\Cell\Park\MonumentOfMining;
use App\Entity\Cell\Park\MonumentOfPeace;
use App\Entity\Cell\Park\MonumentOfWar;
use App\Entity\Cell\Park\MonumentOfWinner;
use App\Entity\Cell\Park\Park;
use App\Entity\Cell\ResourcesProduction\Mine;
use App\Entity\Cell\ResourcesProduction\Oilfield;
use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\Pirate;
use App\Entity\Cell\Ship\Submarine;
use App\Entity\Cell\Ship\TransportShip;

class CellConst
{
    const CELLS = [
        City::TYPE => City::class,
        Factory::TYPE => Factory::class,
        Farm::TYPE => Farm::class,
        FarmDome::TYPE => FarmDome::class,
        Forest::TYPE => Forest::class,
        Metropolis::TYPE => Metropolis::class,
        Mountain::TYPE => Mountain::class,
        Volcano::TYPE => Volcano::class,
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
        TransportShip::TYPE => TransportShip::class,
        Battleship::TYPE => Battleship::class,
        Submarine::TYPE => Submarine::class,
        Pirate::TYPE => Pirate::class,
    ];

    const IS_LAND = 'is_land';
    const IS_MONSTER = 'is_monster';
    const IS_SHIP = 'is_ship';
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

    const ELEVATION_MOUNTAIN = 1;
    const ELEVATION_PLAIN = 0;
    const ELEVATION_SHALLOW = -1;
    const ELEVATION_SEA = -2;

    static public function getClassByType(string $type): string
    {
        return self::CELLS[$type];
    }
}
