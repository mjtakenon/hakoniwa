<?php

namespace App\Entity\Status;

use App\Entity\Cell\Cell;
use App\Entity\Cell\FoodsProduction\Farm;
use App\Entity\Cell\FoodsProduction\FarmDome;
use App\Entity\Cell\FoodsProduction\IFoodsProduction;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\IslandStatus;

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
    private int $fundsProductionCapacity;
    private int $foodsProductionCapacity;
    private int $resourcesProductionCapacity;
    private int $maintenanceNumberOfPeople;
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
        $this->aggregate($terrain, 0);

        return $this;
    }
    public function fromModel(IslandStatus $islandStatus): static
    {
        $this->developmentPoints = $islandStatus->development_points;
        $this->funds = $islandStatus->funds;
        $this->foods = $islandStatus->foods;
        $this->resources = $islandStatus->resources;
        $this->population = $islandStatus->population;
        $this->fundsProductionCapacity = $islandStatus->funds_production_capacity;
        $this->foodsProductionCapacity = $islandStatus->foods_production_capacity;
        $this->resourcesProductionCapacity = $islandStatus->resources_production_capacity;
        $this->maintenanceNumberOfPeople = $islandStatus->maintenance_number_of_people;
        $this->environment = $islandStatus->environment;
        $this->area = $islandStatus->area;
        $this->abandonedTurn = $islandStatus->abandoned_turn;

        return $this;
    }

    public function executeTurn(Terrain $terrain, int $maintenanceNumberOfPeople): static
    {
        $this->aggregate($terrain, $maintenanceNumberOfPeople);

        // 生産人口算出
        $realFundsProductionCapacity = 0;
        $realFoodsProductionCapacity = 0;
        $realResourcesProductionCapacity = 0;

        $workablePeople = $this->population - $maintenanceNumberOfPeople;
        if ($workablePeople < 0) {
            $workablePeople = 0;
        }

        $sumProductionCapacity = $this->foodsProductionCapacity + $this->fundsProductionCapacity + $this->resourcesProductionCapacity;

        if ($sumProductionCapacity > 0) {
            $realFundsProductionCapacity =
                min([((float)($this->fundsProductionCapacity) / $sumProductionCapacity) * $workablePeople, $this->fundsProductionCapacity]);
            $realFoodsProductionCapacity =
                min([((float)($this->foodsProductionCapacity) / $sumProductionCapacity) * $workablePeople, $this->foodsProductionCapacity]);
            $realResourcesProductionCapacity =
                min([((float)($this->resourcesProductionCapacity) / $sumProductionCapacity) * $workablePeople, $this->resourcesProductionCapacity]);
        }

        if ($this->foodsProductionCapacity > 0) {
            // 食料生産
            $farmProductionCapacity = $terrain
                ->findByTypes([Farm::TYPE])
                ->sum(function ($cell) { /** @var IFoodsProduction $cell */ return $cell->getFoodsProductionCapacity(); });

            $farmDomeProductionCapacity = $terrain
                ->findByTypes([FarmDome::TYPE])
                ->sum(function ($cell) { /** @var IFoodsProduction $cell */ return $cell->getFoodsProductionCapacity(); });

            $farmRatio = $farmProductionCapacity / ($farmProductionCapacity + $farmDomeProductionCapacity);
            $farmDomeRatio = $farmDomeProductionCapacity / ($farmProductionCapacity + $farmDomeProductionCapacity);

            $this->producedFoods = $realFoodsProductionCapacity * $farmRatio * self::FOODS_PRODUCTION_COEF[$this->environment];
            $this->producedFoods += $realFoodsProductionCapacity * $farmDomeRatio * self::FOODS_PRODUCTION_COEF[self::ENVIRONMENT_BEST];
            $this->foods += $this->producedFoods;
        }

        // 資源生産
        $this->producedResources = $realResourcesProductionCapacity * self::RESOURCES_PRODUCTION_COEF;
        $this->resources += $this->producedResources;

        // 資金生産
        $realFundsProductionCapacity = min([$this->resources / self::RESOURCES_CONSUMPTION_COEF, $realFundsProductionCapacity]);
        $this->producedFunds = $realFundsProductionCapacity * self::FUNDS_PRODUCTION_COEF;
        $this->funds += $this->producedFunds;
        $this->resources -= $realFundsProductionCapacity * self::RESOURCES_CONSUMPTION_COEF;

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

    public function aggregate(Terrain $terrain, int $maintenanceNumberOfPeople): void
    {
        $this->population = $terrain->aggregatePopulation();
        $this->fundsProductionCapacity = $terrain->aggregateFundsProductionCapacity();
        $this->foodsProductionCapacity = $terrain->aggregateFoodsProductionCapacity();
        $this->resourcesProductionCapacity = $terrain->aggregateResourcesProductionCapacity();
        $this->maintenanceNumberOfPeople = $maintenanceNumberOfPeople;
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

    public function getDevelopmentPoints(): int
    {
        return $this->developmentPoints;
    }

    public function getFunds(): int
    {
        return $this->funds;
    }

    public function setFunds(int $funds): void
    {
        $this->funds = $funds;
    }

    public function setResources(int $resources): void
    {
        $this->resources = $resources;
    }

    public function setDevelopmentPoints(int $developmentPoints): void
    {
        $this->developmentPoints = $developmentPoints;
    }

    public function getFoods(): int
    {
        return $this->foods;
    }

    public function setFoods(int $foods): void
    {
        $this->foods = $foods;
    }

    public function getResources(): int
    {
        return $this->resources;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function getFundsProductionCapacity(): int
    {
        return $this->fundsProductionCapacity;
    }

    public function getFoodsProductionCapacity(): int
    {
        return $this->foodsProductionCapacity;
    }

    public function getResourcesProductionCapacity(): int
    {
        return $this->resourcesProductionCapacity;
    }

    public function getMaintenanceNumberOfPeople(): int
    {
        return $this->maintenanceNumberOfPeople;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getArea(): int
    {
        return $this->area;
    }

    public function getAbandonedTurn(): int
    {
        return $this->abandonedTurn;
    }

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
