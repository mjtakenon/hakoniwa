<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class City extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land5.gif';
    public const TYPE = 'city';
    public const NAME = '都市';

    public const MIN_POPULATION = 10000;
    public const MAX_POPULATION = 20000;

    private const DEFAULT_MIN_POPULATION_INCREMENTAL_RATE = 1;
    private const DEFAULT_MAX_POPULATION_INCREMENTAL_RATE = 10;
    private const FOOD_SHORTAGES_MIN_POPULATION_INCREMENTAL_RATE = -30;
    private const FOOD_SHORTAGES_MAX_POPULATION_INCREMENTAL_RATE = -1;

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => true,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => true,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => true,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_HUGE_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;

        if (array_key_exists('population', $data)) {
            $this->population = $data['population'];
        } else {
            $this->population = self::MIN_POPULATION;
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
                'population' => $this->population,
            ]
        ];
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . self::NAME . PHP_EOL .
            '人口 ' . $this->population . '人';
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        if ($status->getFoods() > 0) {
            $minPopulationIncrementalRate = self::DEFAULT_MIN_POPULATION_INCREMENTAL_RATE;
            $maxPopulationIncrementalRate = self::DEFAULT_MAX_POPULATION_INCREMENTAL_RATE;
        } else {
            $minPopulationIncrementalRate = self::FOOD_SHORTAGES_MIN_POPULATION_INCREMENTAL_RATE;
            $maxPopulationIncrementalRate = self::FOOD_SHORTAGES_MAX_POPULATION_INCREMENTAL_RATE;
        }

        if ($this->population < City::MIN_POPULATION) {
            $this->population += random_int($minPopulationIncrementalRate, $maxPopulationIncrementalRate) * 100;

            if ($this->population >= City::MIN_POPULATION) {
                $this->population = City::MIN_POPULATION;
            }
        } else {
            $this->population += random_int($minPopulationIncrementalRate, $maxPopulationIncrementalRate) * 100;

            // 仮
            if ($this->population >= City::MIN_POPULATION) {
                $this->population = City::MIN_POPULATION;
            }
        }

        if ($this->population >= City::MIN_POPULATION) {
            $terrain->setCell($this->point, new City(point: $this->point, population: $this->population));
            return;
        }

        if ($this->population >= Town::MIN_POPULATION) {
            $terrain->setCell($this->point, new Town(point: $this->point, population: $this->population));
            return;
        }

        if ($this->population >= Village::MIN_POPULATION) {
            $terrain->setCell($this->point, new Village(point: $this->point, population: $this->population));
            return;
        }

        $terrain->setCell($this->point, new Plain(point: $this->point));
    }
}
