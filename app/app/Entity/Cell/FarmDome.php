<?php

namespace App\Entity\Cell;

use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class FarmDome extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land7.gif';
    public const TYPE = 'farm_dome';
    public const NAME = '農場ドーム';
    const PRODUCTION_CAPACITY = 20000;
    const LAKESIDE_PRODUCTION_CAPACITY = 30000;
    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::HAS_POPULATION => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => true,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellConst::DESTRUCTIBLE_BY_RIOT => true,
        CellConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        if (array_key_exists('foodsProductionCapacity', $data)) {
            $this->foodsProductionCapacity = $data['foodsProductionCapacity'];
        } else {
            $this->foodsProductionCapacity = self::PRODUCTION_CAPACITY;
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['foodsProductionCapacity'] = $this->foodsProductionCapacity;
        return $arr;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            $this->foodsProductionCapacity . '人規模';
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $cells = $terrain->getAroundCells($this->point);
        $lakesideCells = $cells->filter(function ($cell) {
            return $cell::TYPE === Lake::TYPE;
        });
        if ($lakesideCells->count() >= 1) {
            $this->foodsProductionCapacity = self::LAKESIDE_PRODUCTION_CAPACITY;
        } else {
            $this->foodsProductionCapacity = self::PRODUCTION_CAPACITY;
        }

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_FARM_CAPACITY_AVAILABLE_POINTS) {
            $this->foodsProductionCapacity *= 2;
        }
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
