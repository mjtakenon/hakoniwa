<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class Farm extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land7.gif';
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
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
        if (array_key_exists('foodsProductionNumberOfPeople', $data)) {
            $this->foodsProductionNumberOfPeople = $data['foodsProductionNumberOfPeople'];
        } else {
            $this->foodsProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
        }
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
                'foodsProductionNumberOfPeople' => $this->foodsProductionNumberOfPeople,
            ]
        ];
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            $this->foodsProductionNumberOfPeople . '人規模';
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $cells = $terrain->getAroundCells($this->point);
        $lakesideCells = $cells->filter(function ($cell) { return $cell::TYPE === Lake::TYPE; });
        if ($lakesideCells->count() >= 1) {
            $this->foodsProductionNumberOfPeople = self::LAKESIDE_PRODUCTION_NUMBER_OF_PEOPLE;
        } else {
            $this->foodsProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
        }
    }
}
