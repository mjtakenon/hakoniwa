<?php

namespace App\Services\Hakoniwa\Status;

use App\Models\IslandStatus;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Terrain\Terrain;

class Status
{
    const INITIAL_DEVELOPMENT_POINTS = 0;
    const INITIAL_FUNDS = 1000;
    const INITIAL_FOODS = 100000;
    const INITIAL_RESOURCES = 0;

    const ENVIRONMENT_NORMAL = 'normal';
    const ENVIRONMENT_GOOD = 'good';
    const ENVIRONMENT_BEST = 'best';

    private int $development_points;
    private int $funds;
    private int $foods;
    private int $resources;
    private int $population;
    private int $funds_production_number_of_people;
    private int $foods_production_number_of_people;
    private int $resources_production_number_of_people;
    private string $environment;
    private int $area;

    public static function create(): static
    {
        return new static;
    }
    public function init(Terrain $terrain): static
    {
        $this->development_points = self::INITIAL_DEVELOPMENT_POINTS;
        $this->funds = self::INITIAL_FUNDS;
        $this->foods = self::INITIAL_FOODS;
        $this->resources = self::INITIAL_RESOURCES;
        $this->aggregate($terrain);

        return $this;
    }
    public function fromModel(IslandStatus $islandStatus): static
    {
        $this->development_points = $islandStatus->development_points;
        $this->funds = $islandStatus->funds;
        $this->foods = $islandStatus->foods;
        $this->resources = $islandStatus->resources;
        $this->population = $islandStatus->population;
        $this->funds_production_number_of_people = $islandStatus->funds_production_number_of_people;
        $this->foods_production_number_of_people = $islandStatus->foods_production_number_of_people;
        $this->resources_production_number_of_people = $islandStatus->resources_production_number_of_people;
        $this->environment = $islandStatus->environment;
        $this->area = $islandStatus->area;

        return $this;
    }

    public function executeTurn(Terrain $terrain): static
    {
        // TODO: 生産と消費実装
        $this->aggregate($terrain);
        return $this;
    }

    private function aggregate(Terrain $terrain)
    {
        $this->population = $terrain->aggregatePopulation();
        $this->funds_production_number_of_people = $terrain->aggregateFundsProductionNumberOfPeople();
        $this->foods_production_number_of_people = $terrain->aggregateFoodsProductionNumberOfPeople();
        $this->resources_production_number_of_people = $terrain->aggregateResourcesProductionNumberOfPeople();
        $this->environment = $terrain->getEnvironment();
        $this->area = $terrain->aggregateArea();
    }

    /**
     * @return int
     */
    public function getDevelopmentPoints(): int
    {
        return $this->development_points;
    }

    /**
     * @return int
     */
    public function getFunds(): int
    {
        return $this->funds;
    }

    /**
     * @return int
     */
    public function getFoods(): int
    {
        return $this->foods;
    }

    /**
     * @return int
     */
    public function getResources(): int
    {
        return $this->resources;
    }

    /**
     * @return int
     */
    public function getPopulation(): int
    {
        return $this->population;
    }

    /**
     * @return int
     */
    public function getFundsProductionNumberOfPeople(): int
    {
        return $this->funds_production_number_of_people;
    }

    /**
     * @return int
     */
    public function getFoodsProductionNumberOfPeople(): int
    {
        return $this->foods_production_number_of_people;
    }

    /**
     * @return int
     */
    public function getResourcesProductionNumberOfPeople(): int
    {
        return $this->resources_production_number_of_people;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return int
     */
    public function getArea(): int
    {
        return $this->area;
    }
}
