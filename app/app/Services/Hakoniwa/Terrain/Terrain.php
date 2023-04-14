<?php

namespace App\Services\Hakoniwa\Terrain;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\Factory;
use App\Services\Hakoniwa\Cell\Forest;
use App\Services\Hakoniwa\Cell\Lake;
use App\Services\Hakoniwa\Cell\Mountain;
use App\Services\Hakoniwa\Cell\Oilfield;
use App\Services\Hakoniwa\Cell\Plain;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Village;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Disaster\DisasterConst;
use App\Services\Hakoniwa\Disaster\DisasterResult;
use App\Services\Hakoniwa\Disaster\IDisaster;
use App\Services\Hakoniwa\JsonEncodable;
use App\Services\Hakoniwa\Log\Logs;
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
            $terrain[$cell->getPoint()->y][$cell->getPoint()->x] = $cell;
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

    public function getCell(Point $point): Cell
    {
        return $this->terrain[$point->y][$point->x];
    }

    public function setCell(Point $point, Cell $cell)
    {
        return $this->terrain[$point->y][$point->x] = $cell;
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

    public function aggregateMaintenanceNumberOfPeople(): int
    {
        $maintenanceNumberOfPeople = [];
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            $maintenanceNumberOfPeople[] = $cell->getMaintenanceNumberOfPeople();
        }
        return array_sum($maintenanceNumberOfPeople);
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

    public function getAroundCells(Point $point, int $range = 1): Collection
    {
        $cells = new Collection();
        if ($point->x >= 1) {
            $cells[] = $this->terrain[$point->y][$point->x-1];
        }
        if ($point->x <= \HakoniwaService::getMaxWidth()-2) {
            $cells[] = $this->terrain[$point->y][$point->x+1];
        }
        if ($point->y >= 1) {
            $cells[] = $this->terrain[$point->y-1][$point->x];
        }
        if ($point->y <= \HakoniwaService::getMaxHeight()-2) {
            $cells[] = $this->terrain[$point->y+1][$point->x];
        }

        // yが偶数 => (+1:-1), (+1:+1)
        if ($point->y % 2 === 0) {
            //
            if ($point->x <= \HakoniwaService::getMaxWidth()-2) {
                if ($point->y >= 1) {
                    $cells[] = $this->terrain[$point->y-1][$point->x+1];
                }
                if ($point->y <= \HakoniwaService::getMaxHeight()-2) {
                    $cells[] = $this->terrain[$point->y+1][$point->x+1];
                }
            }
        } else {
            // yが偶数 => (-1:-1), (-1:+1)
            if ($point->x >= 1) {
                if ($point->y >= 1) {
                    $cells[] = $this->terrain[$point->y-1][$point->x-1];
                }
                if ($point->y <= \HakoniwaService::getMaxHeight()-2) {
                    $cells[] = $this->terrain[$point->y+1][$point->x-1];
                }
            }
        }

        if ($range === 1) {
            return $cells;
        }

        for ($r = 1; $r < $range; $r++) {
            foreach ($cells as $cell) {
                $aroundCells = $this->getAroundCells($cell->getPoint())->reject(function ($cell) use ($point) {
                    return $cell->getPoint()->toString() === $point->toString();
                });
                $cells = $cells->merge($aroundCells);
            }
            $cells = $cells->uniqueStrict(function ($cell) {
                return $cell->getPoint()->toString();
            });
        }

        return $cells;
    }

    public function checkIsLake()
    {
        $isChecked = new Collection();
        $lakeCandidate = new Collection();

        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            if ($cell::TYPE === Lake::TYPE) {
                $this->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
            }
            else if ($cell::ATTRIBUTE[CellTypeConst::IS_LAND]) {
                $isChecked[$cell->getPoint()->toString()] = true;
                continue;
            }

            if ($cell->getPoint()->x === 0 || $cell->getPoint()->x === \HakoniwaService::getMaxWidth()-1 ||
                $cell->getPoint()->y === 0 || $cell->getPoint()->y === \HakoniwaService::getMaxHeight()-1) {
                $isChecked[$cell->getPoint()->toString()] = true;
                $lakeCandidate->push($cell->getPoint());
            } else {
                $isChecked[$cell->getPoint()->toString()] = false;
            }
        }

        while ($lakeCandidate->isNotEmpty()) {
            /** @var Point $point */
            $point = $lakeCandidate->pop();
            $isChecked[$point->toString()] = true;
            $cells = $this->getAroundCells($point);
            foreach($cells as $cell) {
                // 走査済み
                if ($isChecked[$cell->getPoint()->toString()]) {
                    continue;
                }
                // 未走査
                $lakeCandidate->push($cell->getPoint());
                $isChecked[$cell->getPoint()->toString()] = true;
            }
        }

        $isLake = $isChecked->reject(function($val) {
            return $val;
        });

        $isLake->each(function ($val, $key) {
            $point = Point::fromString($key);
            $this->setCell($point, new Lake(point: $point));
        });
    }

    public function occurDisaster(Island $island, Status $status, Turn $turn)
    {
        $logs = Logs::create();

        /** @var IDisaster $disaster */
        foreach (DisasterConst::DISASTERS as $disaster) {
            $disasterResult = $disaster::occur($island, $this, $status, $turn);
            $this->terrain = $disasterResult->getTerrain()->terrain;
            $status = $disasterResult->getStatus();
            $logs->merge($disasterResult->getLogs());
        }

        return new DisasterResult($this, $status, $logs);
    }
}
