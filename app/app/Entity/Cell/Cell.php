<?php

namespace App\Entity\Cell;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

abstract class Cell
{
    public const ATTRIBUTE = [
        CellTypeConst::IS_LAND => false,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::IS_SHIP => false,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => false,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => false,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => false,
    ];

    public const ELEVATION = 0;

    protected string $name;
    protected string $imagePath;
    protected string $type;
    protected Point $point;

    protected int $population = 0;
    protected int $fundsProductionNumberOfPeople = 0;
    protected int $foodsProductionNumberOfPeople = 0;
    protected int $resourcesProductionNumberOfPeople = 0;
    protected int $maintenanceNumberOfPeople = 0;
    protected int $woods = 0;
    protected int $elevation = self::ELEVATION;

    public function __construct(...$data)
    {
        $this->point = new Point($data['point']->x, $data['point']->y);
    }

    public static function create($data)
    {
        return new static($data);
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = [
            'type' => $this->getType(),
            'data' => [
                'point' => $this->getPoint(),
            ]
        ];

        if ($withStatic) {
            $arr['data']['image_path'] = $this->getImagePath();
            $arr['data']['info'] = $this->getInfoString($isPrivate);
        }

        return $arr;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setElevation(int $elevation): void
    {
        $this->elevation = $elevation;
    }

    public function getElevation(): int
    {
        return $this->elevation;
    }

    public function setPoint(Point $point): void
    {
        $this->point = $point;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function setPopulation($population)
    {
        $this->population = $population;
    }

    public function getFoodsProductionNumberOfPeople(): int
    {
        return $this->foodsProductionNumberOfPeople;
    }

    public function getFundsProductionNumberOfPeople(): int
    {
        return $this->fundsProductionNumberOfPeople;
    }

    public function getResourcesProductionNumberOfPeople(): int
    {
        return $this->resourcesProductionNumberOfPeople;
    }

    public function getWoods(): int
    {
        return $this->woods;
    }

    public function getMaintenanceNumberOfPeople(Island $island): int
    {
        return $this->maintenanceNumberOfPeople;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName();
    }

    static public function fromJson(string $type, $data): Cell
    {
        return new (CellTypeConst::getClassByType($type))(...get_object_vars($data));
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}