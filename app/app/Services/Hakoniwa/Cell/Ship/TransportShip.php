<?php

namespace App\Services\Hakoniwa\Cell\Ship;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\MissileBase\IMissileFireable;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class TransportShip extends Ship
{
    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/fune12.gif';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/fune12.gif';
    public const TYPE = 'transport_ship';
    public const NAME = '輸送船';
    const MAINTENANCE_NUMBER_OF_PEOPLE = 1000;

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => false,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::IS_SHIP => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => false,
    ];

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['maintenanceNumberOfPeople'] = self::MAINTENANCE_NUMBER_OF_PEOPLE;
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
            '('. $this->point->x . ',' . $this->point->y .') ' . $this->getName() . PHP_EOL .
            '維持人数' . $this->maintenanceNumberOfPeople . '人' . PHP_EOL;
    }
}