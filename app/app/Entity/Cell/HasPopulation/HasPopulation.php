<?php

namespace App\Entity\Cell\HasPopulation;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Plain;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\DevelopmentPointsConst;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

abstract class HasPopulation extends Cell implements IHasPopulation
{
    private const DEFAULT_MIN_POPULATION_INCREMENTAL_RATE = 1;
    private const DEFAULT_MAX_POPULATION_INCREMENTAL_RATE = 10;
    private const FOOD_SHORTAGES_MIN_POPULATION_INCREMENTAL_RATE = -30;
    private const FOOD_SHORTAGES_MAX_POPULATION_INCREMENTAL_RATE = -1;

    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::HAS_POPULATION => true,
        CellConst::DESTRUCTIBLE_BY_FIRE => true,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => true,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    protected int $minPopulation;
    protected int $maxPopulation;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('population', $data)) {
            $this->population = $data['population'];
        } else {
            $this->population = $this->minPopulation;
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['population'] = $this->population;
        return $arr;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            '人口 ' . $this->population . '人';
    }

    private function isSeaside(Terrain $terrain): bool
    {
        $cells = $terrain->getAroundCells($this->point);
        $seasideCells = $cells->reject(function ($cell) {
            return $cell::ATTRIBUTE[CellConst::IS_LAND];
        });
        return $seasideCells->count() >= 1;
    }

    private function getNaturalIncreasePopulation(Terrain $terrain, Status $status): int
    {
        $maxPopulation = 10000;

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_CITY_CAPACITY_3_AVAILABLE_POINTS) {
            $maxPopulation = 16000;
        } else if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_CITY_CAPACITY_2_AVAILABLE_POINTS) {
            $maxPopulation = 14000;
        } else if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_CITY_CAPACITY_1_AVAILABLE_POINTS) {
            $maxPopulation = 12000;
        }

        // 内陸部の最大人口は沿岸部の半分
        if (!$this->isSeaside($terrain)) {
            $maxPopulation *= 0.5;
        }

        return $maxPopulation;
    }

    private function getMaxPopulation(Status $status): int
    {
        $maxPopulation = 20000;

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_CITY_CAPACITY_3_AVAILABLE_POINTS) {
            $maxPopulation = 30000;
        } else if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_CITY_CAPACITY_2_AVAILABLE_POINTS) {
            $maxPopulation = 26000;
        } else if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_CITY_CAPACITY_1_AVAILABLE_POINTS) {
            $maxPopulation = 23000;
        }

        return $maxPopulation;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if ($status->getFoods() > 0) {
            // 通常時
            $minPopulationIncrementalRate = self::DEFAULT_MIN_POPULATION_INCREMENTAL_RATE;
            $maxPopulationIncrementalRate = self::DEFAULT_MAX_POPULATION_INCREMENTAL_RATE;
            // 環境が通常の場合、人口増加率を半分にする
            if ($status->getEnvironment() === Status::ENVIRONMENT_NORMAL) {
                $minPopulationIncrementalRate *= 0.5;
                $maxPopulationIncrementalRate *= 0.5;
            }
        } else {
            // 食料不足時
            $minPopulationIncrementalRate = self::FOOD_SHORTAGES_MIN_POPULATION_INCREMENTAL_RATE;
            $maxPopulationIncrementalRate = self::FOOD_SHORTAGES_MAX_POPULATION_INCREMENTAL_RATE;
        }

        $naturalIncreasePopulation = $this->getNaturalIncreasePopulation($terrain, $status);
        // 自然増加/減少
        if ($this->population < $naturalIncreasePopulation) {
            $this->population += random_int($minPopulationIncrementalRate, $maxPopulationIncrementalRate) * 100;

            if ($this->population >= $naturalIncreasePopulation) {
                $this->population = $naturalIncreasePopulation;
            }
        } else {
            if ($status->getFoods() > 0) {
                // 自然増加上限を超えている場合、かつ食料がある場合は自然増加しない
                $maxPopulation = $this->getMaxPopulation($status);
                if ($this->population >= $maxPopulation) {
                    $this->population = $maxPopulation;
                }
            } else {
                // 食糧不足時には減少させる
                $this->population += random_int($minPopulationIncrementalRate, $maxPopulationIncrementalRate) * 100;
            }
        }

        // マップチップ入れ替え
        if ($this->population >= Metropolis::MIN_POPULATION) {
            $terrain->setCell($this->point, new Metropolis(point: $this->point, population: $this->population));
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->population >= City::MIN_POPULATION) {
            $terrain->setCell($this->point, new City(point: $this->point, population: $this->population));
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->population >= Town::MIN_POPULATION) {
            $terrain->setCell($this->point, new Town(point: $this->point, population: $this->population));
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->population >= Village::MIN_POPULATION) {
            $terrain->setCell($this->point, new Village(point: $this->point, population: $this->population));
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        $terrain->setCell($this->point, new Plain(point: $this->point));
        return new PassTurnResult($terrain, $status, Logs::create());
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function setPopulation($population): void
    {
        $this->population = $population;
    }
}
