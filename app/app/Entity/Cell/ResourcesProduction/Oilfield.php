<?php

namespace App\Entity\Cell\ResourcesProduction;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Oilfield extends Cell implements IResourcesProduction
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land16.gif';
    public const TYPE = 'oilfield';
    public const NAME = '油田';
    const PRODUCTION_CAPACITY = 20000;
    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellConst::DESTRUCTIBLE_BY_RIOT => true,
        CellConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => false,
    ];
    public const ELEVATION = CellConst::ELEVATION_SHALLOW;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $elevation = self::ELEVATION;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('resourcesProductionCapacity', $data)) {
            $this->resourcesProductionCapacity = $data['resourcesProductionCapacity'];
        } else {
            $this->resourcesProductionCapacity = self::PRODUCTION_CAPACITY;
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['resourcesProductionCapacity'] = $this->resourcesProductionCapacity;
        return $arr;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            $this->resourcesProductionCapacity . '人規模';
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $this->resourcesProductionCapacity = self::PRODUCTION_CAPACITY;

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_MINE_AND_OILFIELD_CAPACITY_AVAILABLE_POINTS) {
            $this->resourcesProductionCapacity *= 2;
        }

        return new PassTurnResult($terrain, $status, Logs::create());
    }

    public function getResourcesProductionCapacity(): int
    {
        return $this->resourcesProductionCapacity;
    }
}
