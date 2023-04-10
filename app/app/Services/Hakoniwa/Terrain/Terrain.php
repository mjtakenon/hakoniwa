<?php

namespace App\Services\Hakoniwa\Terrain;

use App\Models\Island;
use App\Models\IslandStatus;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Oilfield;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\JsonEncodable;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Util\Normal;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Support\Collection;

class Terrain implements JsonEncodable
{
    private Collection $terrain;

    public function __construct()
    {
        $terrain = new Collection();
        for ($y = 0; $y < \HakoniwaService::getMaxWidth(); $y++) {
            $row = new Collection();
            for ($x = 0; $x < \HakoniwaService::getMaxHeight(); $x++) {
                $row[] = new Sea(point: new Point($x, $y));
            }
            $terrain[] = $row;
        }
        $this->terrain = $terrain;
    }

    public static function create(): Terrain
    {
        return new static;
    }

    public function toJson(): string
    {
        $terrain = [];
        foreach ($this->terrain as $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                $terrain[] = $cell->toArray();
            }
        }
        return json_encode($terrain);
    }

    public static function fromJson(string $json): Terrain
    {
        $objects = json_decode($json);

        $terrain = new Collection();
        for ($y = 0; $y < \HakoniwaService::getMaxWidth(); $y++) {
            $row = new Collection();
            for ($x = 0; $x < \HakoniwaService::getMaxHeight(); $x++) {
                $row[] = null;
            }
            $terrain[] = $row;
        }

        foreach ($objects as $object) {
            /** @var Cell $cell */
            $cell = Cell::fromJson($object->type, $object->data);
            $terrain[$cell->getPoint()->x][$cell->getPoint()->y] = $cell;
        }

        $static = new static();
        $static->setTerrain($terrain);
        return $static;
    }

    public function init(): Terrain
    {
        $n = 0;
        while (true) {
            $x = (int)Normal::normal(\HakoniwaService::getMaxWidth() / 2, \HakoniwaService::getMaxWidth() / 11);
            $y = (int)Normal::normal(\HakoniwaService::getMaxHeight() / 2, \HakoniwaService::getMaxHeight() / 11);

            if ($this->terrain[$y][$x]::TYPE === 'sea') {
                if ($n < 4) {
                    $this->terrain[$y][$x] = new Forest(point: new Point($x, $y));
                } else if ($n < 18) {
                    $this->terrain[$y][$x] = new Wasteland(point: new Point($x, $y));
                } else if ($n < 19) {
                    $this->terrain[$y][$x] = new Mountain(point: new Point($x, $y));
                } else if ($n < 21) {
                    $this->terrain[$y][$x] = new Village(point: new Point($x, $y), population: 1000);
                } else if ($n < 28) {
                    $this->terrain[$y][$x] = new Plain(point: new Point($x, $y));
                } else if ($n < 38) {
                    $this->terrain[$y][$x] = new Shallow(point: new Point($x, $y));
//                } else if ($n < 39) {
//                    $this->terrain[$y][$x] = new MissileBase(new Point($x, $y));
                } else {
                    break;
                }
                $n++;
            }
        }

        return $this;
    }

    public function getTerrain(): Collection
    {
        return $this->terrain;
    }

    public function setTerrain($terrain)
    {
        $this->terrain = $terrain;
    }

    public function aggregatePopulation(): int
    {
        $population = [];
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            $population[] = $cell->getPopulation();
        }
        return array_sum($population);
    }

    public function aggregateFundsProductionNumberOfPeople(): int
    {
        $fundsProductionNumberOfPeople = [];
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            $fundsProductionNumberOfPeople[] = $cell->getFundsProductionNumberOfPeople();
        }
        return array_sum($fundsProductionNumberOfPeople);
    }

    public function aggregateFoodsProductionNumberOfPeople(): int
    {
        $foodsProductionNumberOfPeople = [];
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            $foodsProductionNumberOfPeople[] = $cell->getFoodsProductionNumberOfPeople();
        }
        return array_sum($foodsProductionNumberOfPeople);
    }

    public function aggregateResourcesProductionNumberOfPeople(): int
    {
        $resourcesProductionNumberOfPeople = [];
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            $resourcesProductionNumberOfPeople[] = $cell->getResourcesProductionNumberOfPeople();
        }
        return array_sum($resourcesProductionNumberOfPeople);
    }

    public function getEnvironment(): string
    {
        $hasFactory = false;
        $hasOilField = false;

        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            if ($cell::TYPE === Factory::TYPE) {
                $hasFactory = true;
            }
            if ($cell::TYPE === Oilfield::TYPE) {
                $hasOilField = true;
            }
        }

        if ($hasFactory && $hasOilField) {
            return Status::ENVIRONMENT_NORMAL;
        }
        if (!$hasFactory && !$hasOilField) {
            return Status::ENVIRONMENT_BEST;
        }
        return Status::ENVIRONMENT_GOOD;
    }

    public function aggregateArea(): int
    {
        $area = 0;
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            if ($cell::ATTRIBUTE[CellTypeConst::IS_LAND]) {
                $area += 100;
            }
        }
        return $area;
    }

    public function passTime(Island $island, Status $status): Terrain
    {
        foreach ($this->terrain->flatten(1) as $cell) {
            $cell->passTime($island, $this, $status);
        }

        return $this;
    }
}
