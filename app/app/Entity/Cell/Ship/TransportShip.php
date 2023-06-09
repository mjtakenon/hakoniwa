<?php

namespace App\Entity\Cell\Ship;

use App\Entity\Cell\CellConst;
use App\Entity\Cell\IHasMaintenanceNumberOfPeople;
use App\Entity\Cell\MaintenanceInfo;
use App\Models\Island;

class TransportShip extends Ship implements IHasMaintenanceNumberOfPeople
{
    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/transport_ship_sea.png';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/transport_ship_shallow.png';
    public const TYPE = 'transport_ship';
    public const NAME = '輸送船';
    public const MAINTENANCE_NUMBER_OF_PEOPLE = 1000;

    const ATTRIBUTE = [
        CellConst::IS_LAND => false,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => true,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => true,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => false,
    ];

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['maintenanceNumberOfPeople'] = $this->maintenanceNumberOfPeople;
        return $arr;
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            '維持人数' . $this->maintenanceNumberOfPeople . '人' . PHP_EOL;
    }

    public function getMaintenanceNumberOfPeople(Island $island): MaintenanceInfo
    {
        return new MaintenanceInfo($island->id, $this->maintenanceNumberOfPeople);
    }
}
