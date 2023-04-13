<?php

namespace App\Services\Hakoniwa\Status;

use App\Models\IslandStatus;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Terrain\Terrain;

class Status
{
    private const INITIAL_DEVELOPMENT_POINTS = 0;
    private const INITIAL_FUNDS = 3000;
    private const INITIAL_FOODS = 100000;
    private const INITIAL_RESOURCES = 50000;

    public const ENVIRONMENT_NORMAL = 'normal';
    public const ENVIRONMENT_GOOD = 'good';
    public const ENVIRONMENT_BEST = 'best';
    public const ENVIRONMENT = [
        self::ENVIRONMENT_BEST => '最高',
        self::ENVIRONMENT_GOOD => '良好',
        self::ENVIRONMENT_NORMAL => '通常',
    ];

    private const FOODS_PRODUCTION_COEF = 0.6;
    private const FOODS_CONSUMPTION_COEF = 0.1;
    private const RESOURCES_PRODUCTION_COEF = 0.02;
    private const RESOURCES_CONSUMPTION_COEF = 0.02;
    private const FUNDS_PRODUCTION_COEF = 0.002;


    private int $developmentPoints;
    private int $funds;
    private int $foods;
    private int $resources;
    private int $population;
    private int $fundsProductionNumberOfPeople;
    private int $foodsProductionNumberOfPeople;
    private int $resourcesProductionNumberOfPeople;
    private string $environment;
    private int $area;

    public static function create(): static
    {
        return new static;
    }
    public function init(Terrain $terrain): static
    {
        $this->developmentPoints = self::INITIAL_DEVELOPMENT_POINTS;
        $this->funds = self::INITIAL_FUNDS;
        $this->foods = self::INITIAL_FOODS;
        $this->resources = self::INITIAL_RESOURCES;
        $this->aggregate($terrain);

        return $this;
    }
    public function fromModel(IslandStatus $islandStatus): static
    {
        $this->developmentPoints = $islandStatus->development_points;
        $this->funds = $islandStatus->funds;
        $this->foods = $islandStatus->foods;
        $this->resources = $islandStatus->resources;
        $this->population = $islandStatus->population;
        $this->fundsProductionNumberOfPeople = $islandStatus->funds_production_number_of_people;
        $this->foodsProductionNumberOfPeople = $islandStatus->foods_production_number_of_people;
        $this->resourcesProductionNumberOfPeople = $islandStatus->resources_production_number_of_people;
        $this->environment = $islandStatus->environment;
        $this->area = $islandStatus->area;

        return $this;
    }

    public function executeTurn(Terrain $terrain): static
    {
        $this->aggregate($terrain);

        // 生産人口算出
        $realFundsProductionNumberOfPeople = 0;
        $realFoodsProductionNumberOfPeople = 0;
        $realResourcesProductionNumberOfPeople = 0;

        $workablePeople = $this->population - $terrain->aggregateMaintenanceNumberOfPeople();
        $sumProductionNumberOfPeople = $this->foodsProductionNumberOfPeople + $this->fundsProductionNumberOfPeople + $this->resourcesProductionNumberOfPeople;

        if ($sumProductionNumberOfPeople > 0) {
            $realFundsProductionNumberOfPeople =
                min([((float)($this->fundsProductionNumberOfPeople) / $sumProductionNumberOfPeople) * $workablePeople, $this->fundsProductionNumberOfPeople]);
            $realFoodsProductionNumberOfPeople =
                min([((float)($this->foodsProductionNumberOfPeople) / $sumProductionNumberOfPeople) * $workablePeople, $this->foodsProductionNumberOfPeople]);
            $realResourcesProductionNumberOfPeople =
                min([((float)($this->resourcesProductionNumberOfPeople) / $sumProductionNumberOfPeople) * $workablePeople, $this->resourcesProductionNumberOfPeople]);
        }

        // 食料生産
        if ($this->environment === self::ENVIRONMENT_BEST) {
            $this->foods += $realFoodsProductionNumberOfPeople * self::FOODS_PRODUCTION_COEF * 2;
        } else if ($this->environment === self::ENVIRONMENT_GOOD) {
            $this->foods += $realFoodsProductionNumberOfPeople * self::FOODS_PRODUCTION_COEF;
        } else {
            $this->foods += $realFoodsProductionNumberOfPeople * self::FOODS_PRODUCTION_COEF * 0.5;
        }

        // 資源生産
        $this->resources = $realResourcesProductionNumberOfPeople * self::RESOURCES_PRODUCTION_COEF;

        // 資金生産
        $realFundsProductionNumberOfPeople = min([$this->resources / self::RESOURCES_CONSUMPTION_COEF, $realFundsProductionNumberOfPeople]);
        $this->funds += $realFundsProductionNumberOfPeople * self::FUNDS_PRODUCTION_COEF;
        $this->resources -= $realFundsProductionNumberOfPeople * self::RESOURCES_CONSUMPTION_COEF;

        // 食料消費
        $this->foods -= $this->population * self::FOODS_CONSUMPTION_COEF;

        $this->foods = (int)round($this->foods);
        $this->funds = (int)round($this->funds);
        $this->resources = (int)round($this->resources);

        $this->developmentPoints += $this->sumDevelopmentPoints($terrain);

        return $this;
    }

    public function aggregate(Terrain $terrain)
    {
        $this->population = $terrain->aggregatePopulation();
        $this->fundsProductionNumberOfPeople = $terrain->aggregateFundsProductionNumberOfPeople();
        $this->foodsProductionNumberOfPeople = $terrain->aggregateFoodsProductionNumberOfPeople();
        $this->resourcesProductionNumberOfPeople = $terrain->aggregateResourcesProductionNumberOfPeople();
        $this->environment = $terrain->getEnvironment();
        $this->area = $terrain->aggregateArea();
    }

    /**
     * @return int
     */
    public function getDevelopmentPoints(): int
    {
        return $this->developmentPoints;
    }

    /**
     * @return int
     */
    public function getFunds(): int
    {
        return $this->funds;
    }

    /**
     * @param int $funds
     */
    public function setFunds(int $funds): void
    {
        $this->funds = $funds;
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
        return $this->fundsProductionNumberOfPeople;
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
    public function getResourcesProductionNumberOfPeople(): int
    {
        return $this->resourcesProductionNumberOfPeople;
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

    private function sumDevelopmentPoints(Terrain $terrain): int
    {
        // TODO: 公園
        return (int)round($this->population/200);
    }
}
