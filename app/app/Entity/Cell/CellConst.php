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
use App\Entity\Cell\Monster\Begenoth;
use App\Entity\Cell\Monster\DarkInora;
use App\Entity\Cell\Monster\GhostInora;
use App\Entity\Cell\Monster\Hamunemu;
use App\Entity\Cell\Monster\Inora;
use App\Entity\Cell\Monster\KingInora;
use App\Entity\Cell\Monster\Kujira;
use App\Entity\Cell\Monster\Levinoth;
use App\Entity\Cell\Monster\RedInora;
use App\Entity\Cell\Monster\Sanjira;
use App\Entity\Cell\Monster\Slime;
use App\Entity\Cell\Monster\SlimeLegend;
use App\Entity\Cell\Others\Egg;
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
use App\Entity\Cell\Park\MonumentOfConquest;
use App\Entity\Cell\Park\Park;
use App\Entity\Cell\ResourcesProduction\Mine;
use App\Entity\Cell\ResourcesProduction\Oilfield;
use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\LevinothBattleship;
use App\Entity\Cell\Ship\LevinothSubmarine;
use App\Entity\Cell\Ship\Pirate;
use App\Entity\Cell\Ship\Submarine;
use App\Entity\Cell\Ship\TransportShip;
use App\Entity\Util\Point;

class CellConst
{
    const IS_LAND = 'is_land';
    const IS_MONSTER = 'is_monster';
    const IS_SHIP = 'is_ship';
    const IS_MOUNTAIN = 'is_mountain';

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

    const ELEVATION_LAND = 0;
    const ELEVATION_SHALLOW = -1;
    const ELEVATION_SEA = -2;

    static public function getClassByType(string $type, object $data): Cell
    {
        return match($type) {
            City::TYPE => new City(...get_object_vars($data)),
            Factory::TYPE => new Factory(...get_object_vars($data)),
            Farm::TYPE => new Farm(...get_object_vars($data)),
            FarmDome::TYPE => new FarmDome(...get_object_vars($data)),
            Forest::TYPE => new Forest(...get_object_vars($data)),
            Metropolis::TYPE => new Metropolis(...get_object_vars($data)),
            Mountain::TYPE => new Mountain(...get_object_vars($data)),
            Volcano::TYPE => new Volcano(...get_object_vars($data)),
            Mine::TYPE => new Mine(...get_object_vars($data)),
            Oilfield::TYPE => new Oilfield(...get_object_vars($data)),
            Plain::TYPE => new Plain(...get_object_vars($data)),
            Sea::TYPE => new Sea(...get_object_vars($data)),
            Shallow::TYPE => new Shallow(...get_object_vars($data)),
            Lake::TYPE => new Lake(...get_object_vars($data)),
            LargeFactory::TYPE => new LargeFactory(...get_object_vars($data)),
            Town::TYPE => new Town(...get_object_vars($data)),
            Village::TYPE => new Village(...get_object_vars($data)),
            Wasteland::TYPE => new Wasteland(...get_object_vars($data)),
            MissileBase::TYPE => new MissileBase(...get_object_vars($data)),
            SeabedBase::TYPE => new SeabedBase(...get_object_vars($data)),
            Park::TYPE => new Park(...get_object_vars($data)),
            MonumentOfAgriculture::TYPE => new MonumentOfAgriculture(...get_object_vars($data)),
            MonumentOfMining::TYPE => new MonumentOfMining(...get_object_vars($data)),
            MonumentOfMaster::TYPE => new MonumentOfMaster(...get_object_vars($data)),
            MonumentOfPeace::TYPE => new MonumentOfPeace(...get_object_vars($data)),
            MonumentOfWar::TYPE => new MonumentOfWar(...get_object_vars($data)),
            MonumentOfConquest::TYPE => new MonumentOfConquest(...get_object_vars($data)),
            Inora::TYPE => new Inora(...get_object_vars($data)),
            RedInora::TYPE => new RedInora(...get_object_vars($data)),
            DarkInora::TYPE => new DarkInora(...get_object_vars($data)),
            KingInora::TYPE => new KingInora(...get_object_vars($data)),
            Sanjira::TYPE => new Sanjira(...get_object_vars($data)),
            Kujira::TYPE => new Kujira(...get_object_vars($data)),
            Hamunemu::TYPE => new Hamunemu(...get_object_vars($data)),
            GhostInora::TYPE => new GhostInora(...get_object_vars($data)),
            Slime::TYPE => new Slime(...get_object_vars($data)),
            SlimeLegend::TYPE => new SlimeLegend(...get_object_vars($data)),
            Levinoth::TYPE => new Levinoth(...get_object_vars($data)),
            Begenoth::TYPE => new Begenoth(...get_object_vars($data)),
            Egg::TYPE => new Egg(...get_object_vars($data)),
            TransportShip::TYPE => new TransportShip(...get_object_vars($data)),
            Battleship::TYPE => new Battleship(...get_object_vars($data)),
            Submarine::TYPE => new Submarine(...get_object_vars($data)),
            Pirate::TYPE => new Pirate(...get_object_vars($data)),
            LevinothBattleship::TYPE => new LevinothBattleship(...get_object_vars($data)),
            LevinothSubmarine::TYPE => new LevinothSubmarine(...get_object_vars($data)),
        };
    }

    static public function getDefaultCell(Point $point, int $elevation): Cell
    {
        return match(true) {
            $elevation >= self::ELEVATION_LAND => new Wasteland(point: $point, elevation: $elevation),
            $elevation === self::ELEVATION_SHALLOW => new Shallow(point: $point, elevation: $elevation),
            $elevation <= self::ELEVATION_SEA => new Sea(point: $point, elevation: $elevation),
        };
    }
}
