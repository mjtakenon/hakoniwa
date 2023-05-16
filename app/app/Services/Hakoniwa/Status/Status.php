<?php

namespace App\Services\Hakoniwa\Status;

use App\Models\Island;
use App\Models\IslandStatus;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Farm;
use App\Services\Hakoniwa\Cell\FarmDome;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Terrain\Terrain;

class Status
{
    private const INITIAL_DEVELOPMENT_POINTS = 0;
    private const INITIAL_FUNDS = 3000;
    private const INITIAL_FOODS = 100000;
    private const INITIAL_RESOURCES = 50000;
    private const MAX_FUNDS = 99999;
    private const MAX_FOODS = 9999999;
    private const MAX_RESOURCES = 9999999;

    public const ENVIRONMENT_NORMAL = 'normal';
    public const ENVIRONMENT_GOOD = 'good';
    public const ENVIRONMENT_BEST = 'best';
    private const FOODS_PRODUCTION_COEF = [
        self::ENVIRONMENT_NORMAL => 0.3,
        self::ENVIRONMENT_GOOD => 0.6,
        self::ENVIRONMENT_BEST => 1.2,
    ];
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
    private int $abandonedTurn = 0;

    private int $producedFoods = 0;
    private int $producedFunds = 0;
    private int $producedResources = 0;

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
        $this->abandonedTurn = $islandStatus->abandoned_turn;

        return $this;
    }

    public function executeTurn(Terrain $terrain, Island $island): static
    {
        $this->aggregate($terrain);

        // 生産人口算出
        $realFundsProductionNumberOfPeople = 0;
        $realFoodsProductionNumberOfPeople = 0;
        $realResourcesProductionNumberOfPeople = 0;

        $workablePeople = $this->population - $terrain->aggregateMaintenanceNumberOfPeople($island);
        $sumProductionNumberOfPeople = $this->foodsProductionNumberOfPeople + $this->fundsProductionNumberOfPeople + $this->resourcesProductionNumberOfPeople;

        if ($sumProductionNumberOfPeople > 0) {
            $realFundsProductionNumberOfPeople =
                min([((float)($this->fundsProductionNumberOfPeople) / $sumProductionNumberOfPeople) * $workablePeople, $this->fundsProductionNumberOfPeople]);
            $realFoodsProductionNumberOfPeople =
                min([((float)($this->foodsProductionNumberOfPeople) / $sumProductionNumberOfPeople) * $workablePeople, $this->foodsProductionNumberOfPeople]);
            $realResourcesProductionNumberOfPeople =
                min([((float)($this->resourcesProductionNumberOfPeople) / $sumProductionNumberOfPeople) * $workablePeople, $this->resourcesProductionNumberOfPeople]);
        }

        if ($this->foodsProductionNumberOfPeople > 0) {
            // 食料生産
            $farmProductionNumberOfPeople = $terrain
                ->findByTypes([Farm::TYPE])
                ->sum(function ($cell) { /** @var Cell $cell */ return $cell->getFoodsProductionNumberOfPeople(); });

            $farmDomeProductionNumberOfPeople = $terrain
                ->findByTypes([FarmDome::TYPE])
                ->sum(function ($cell) { /** @var Cell $cell */ return $cell->getFoodsProductionNumberOfPeople(); });

            $farmRatio = $farmProductionNumberOfPeople / ($farmProductionNumberOfPeople + $farmDomeProductionNumberOfPeople);
            $farmDomeRatio = $farmDomeProductionNumberOfPeople / ($farmProductionNumberOfPeople + $farmDomeProductionNumberOfPeople);

            $this->producedFoods = $realFoodsProductionNumberOfPeople * $farmRatio * self::FOODS_PRODUCTION_COEF[$this->environment];
            $this->producedFoods += $realFoodsProductionNumberOfPeople * $farmDomeRatio * self::FOODS_PRODUCTION_COEF[self::ENVIRONMENT_BEST];
            $this->foods += $this->producedFoods;
        }

        // 資源生産
        $this->producedResources = $realResourcesProductionNumberOfPeople * self::RESOURCES_PRODUCTION_COEF;
        $this->resources += $this->producedResources;

        // 資金生産
        $realFundsProductionNumberOfPeople = min([$this->resources / self::RESOURCES_CONSUMPTION_COEF, $realFundsProductionNumberOfPeople]);
        $this->producedFunds = $realFundsProductionNumberOfPeople * self::FUNDS_PRODUCTION_COEF;
        $this->funds += $this->producedFunds;
        $this->resources -= $realFundsProductionNumberOfPeople * self::RESOURCES_CONSUMPTION_COEF;

        // 食料消費
        $this->foods -= $this->population * self::FOODS_CONSUMPTION_COEF;

        if ($this->foods <= 0) {
            $this->foods = 0;
        }

        $this->foods = (int)round($this->foods);
        $this->funds = (int)round($this->funds);
        $this->resources = (int)round($this->resources);

        $this->developmentPoints += $this->sumDevelopmentPoints();

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

    public function truncateOverflows(): void
    {
        if ($this->foods >= self::MAX_FOODS) {
            $this->foods = self::MAX_FOODS;
        }

        if ($this->funds >= self::MAX_FUNDS) {
            $this->funds = self::MAX_FUNDS;
        }

        if ($this->resources >= self::MAX_RESOURCES) {
            $this->resources = self::MAX_RESOURCES;
        }
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
     * @param int $resources
     */
    public function setResources(int $resources): void
    {
        $this->resources = $resources;
    }

    /**
     * @param int $developmentPoints
     */
    public function setDevelopmentPoints(int $developmentPoints): void
    {
        $this->developmentPoints = $developmentPoints;
    }

    /**
     * @return int
     */
    public function getFoods(): int
    {
        return $this->foods;
    }

    /**
     * @param int $foods
     */
    public function setFoods(int $foods): void
    {
        $this->foods = $foods;
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

    public function getAbandonedTurn(): int
    {
        return $this->abandonedTurn;
    }

    /**
     * @param int $abandonedTurn
     */
    public function setAbandonedTurn(int $abandonedTurn): void
    {
        $this->abandonedTurn = $abandonedTurn;
    }

    public function getProducedFoods(): int
    {
        return $this->producedFoods;
    }

    public function getProducedFunds(): int
    {
        return $this->producedFunds;
    }

    public function getProducedResources(): int
    {
        return $this->producedResources;
    }

    private function sumDevelopmentPoints(): int
    {
        return (int)round($this->population/200);
    }
}
