<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class Farm extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land72.gif';
    public const TYPE = 'farm';
    public const NAME = '農場';
    const PRODUCTION_NUMBER_OF_PEOPLE = 20000;
    const LAKESIDE_PRODUCTION_NUMBER_OF_PEOPLE = 30000;
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => true,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('foodsProductionNumberOfPeople', $data)) {
            $this->foodsProductionNumberOfPeople = $data['foodsProductionNumberOfPeople'];
        } else {
            $this->foodsProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
        }
    }

    public function toArray(bool $isPrivate = false): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString($isPrivate),
                'foodsProductionNumberOfPeople' => $this->foodsProductionNumberOfPeople,
            ]
        ];
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getImagePath(): string
    {
        return self::IMAGE_PATH;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            $this->foodsProductionNumberOfPeople . '人規模';
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $cells = $terrain->getAroundCells($this->point);
        $lakesideCells = $cells->filter(function ($cell) {
            return $cell::TYPE === Lake::TYPE;
        });
        if ($lakesideCells->count() >= 1) {
            $this->foodsProductionNumberOfPeople = self::LAKESIDE_PRODUCTION_NUMBER_OF_PEOPLE;
        } else {
            $this->foodsProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
        }

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_FARM_CAPACITY_AVAILABLE_POINTS) {
            $this->foodsProductionNumberOfPeople *= 2;
        }
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
