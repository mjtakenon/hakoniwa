<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\IslandStatus;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

abstract class Cell
{
    public const IMAGE_PATH = '';
    public const TYPE = '';
    public const NAME = '';

    protected ?string $imagePath = null;
    protected ?string $type;
    protected Point $point;

    protected int $population = 0;
    protected int $fundsProductionNumberOfPeople = 0;
    protected int $foodsProductionNumberOfPeople = 0;
    protected int $resourcesProductionNumberOfPeople = 0;
    protected int $maintenanceNumberOfPeople = 0;
    protected int $woods = 0;
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => false,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => true,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
    ];

    public function __construct(...$data)
    {
        $this->point = new Point($data['point']->x, $data['point']->y);
    }

    public static function create($data)
    {
        return new static($data);
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
            ]
        ];
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getInfoString(): string
    {
        return '';
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    /**
     * @return int
     */
    public function getFoodsProductionNumberOfPeople(): int
    {
        return $this->foodsProductionNumberOfPeople;
    }

    /**
     * @return int
     */
    public function getFundsProductionNumberOfPeople(): int
    {
        return $this->fundsProductionNumberOfPeople;
    }

    /**
     * @return int
     */
    public function getResourcesProductionNumberOfPeople(): int
    {
        return $this->resourcesProductionNumberOfPeople;
    }

    public function getWoods(): int
    {
        return $this->woods;
    }

    /**
     * @return int
     */
    public function getMaintenanceNumberOfPeople(): int
    {
        return $this->maintenanceNumberOfPeople;
    }

    static public function fromJson(string $type, $data): Cell
    {
        return new (CellTypeConst::getClassByType($type))(...get_object_vars($data));
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
    }
}
