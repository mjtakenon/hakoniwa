<?php

namespace App\Entity\Terrain;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellTypeConst;
use App\Entity\Cell\Factory;
use App\Entity\Cell\Forest;
use App\Entity\Cell\HasPopulation\Village;
use App\Entity\Cell\Lake;
use App\Entity\Cell\LargeFactory;
use App\Entity\Cell\Mountain;
use App\Entity\Cell\OutOfRegion;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Cell\Plain;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Cell\Volcano;
use App\Entity\Cell\Wasteland;
use App\Entity\Disaster\DisasterConst;
use App\Entity\Disaster\DisasterResult;
use App\Entity\Disaster\IDisaster;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Util\Normal;
use App\Entity\Util\Point;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\JsonEncodable;
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

    public function toJson(bool $isPrivate = false, bool $withStatic = false): string
    {
        return json_encode($this->toArray($isPrivate, $withStatic));
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $terrain = [];
        foreach ($this->terrain as $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                $terrain[] = $cell->toArray($isPrivate, $withStatic);
            }
        }
        return $terrain;
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
                    $this->terrain[$y][$x] = new Volcano(point: new Point($x, $y));
                } else if ($n < 21) {
                    $this->terrain[$y][$x] = new Village(point: new Point($x, $y), population: 1000);
                } else if ($n < 28) {
                    $this->terrain[$y][$x] = new Plain(point: new Point($x, $y));
                } else if ($n < 38) {
                    $this->terrain[$y][$x] = new Shallow(point: new Point($x, $y));
                } else {
                    break;
                }
                $n++;
            }
        }

        return $this->replaceShallowToLake();
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

    public function aggregateMaintenanceNumberOfPeople(Island $island): int
    {
        $maintenanceNumberOfPeople = [];
        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            $maintenanceNumberOfPeople[] = $cell->getMaintenanceNumberOfPeople($island);
        }
        return array_sum($maintenanceNumberOfPeople);
    }

    public function getEnvironment(): string
    {
        $hasFactory = $this->findByTypes([Factory::TYPE, LargeFactory::TYPE])->isNotEmpty();
        $hasOilField = $this->findByTypes([Factory::TYPE, LargeFactory::TYPE])->isNotEmpty();

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
        /** @var Cell $cell */
        $landCells = $this->findByAttribute(CellTypeConst::IS_LAND)->count();
        return $landCells * 100;
    }

    public function passTurn(Island $island, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $logs = Logs::create();

        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {

            // 怪獣の移動などで既に行動されている場合、セルのTypeが変わるため処理を行わない
            if ($cell->getType() !== $this->getCell($cell->getPoint())->getType()) {
                continue;
            }

            if ($cell::ATTRIBUTE[CellTypeConst::IS_MONSTER] && (float)config('app.hakoniwa.monster_action_probably') <= Rand::mt_rand_float()) {
                continue;
            }

            $passTurnResult = $cell->passTurn($island, $this, $status, $turn, $foreignIslandOccurEvents);

            $this->terrain = $passTurnResult->getTerrain()->getTerrain();
            $status = $passTurnResult->getStatus();
            $logs->merge($passTurnResult->getLogs());
        }

        return new PassTurnResult($this, $status, $logs);
    }

    private function inRange(int $n, int $min, int $max): bool
    {
        return $n >= $min && $n < $max;
    }

    public function getAroundCells(Point $point, int $range = 1, bool $includeOutOfRegion = false): Collection
    {
        $cells = new Collection();

        if ($this->inRange($point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x - 1, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->terrain[$point->y][$point->x - 1];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x - 1, $point->y));
        }

        if ($this->inRange($point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x + 1, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->terrain[$point->y][$point->x + 1];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x + 1, $point->y));
        }

        if ($this->inRange($point->y - 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->terrain[$point->y - 1][$point->x];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x, $point->y - 1));
        }

        if ($this->inRange($point->y + 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->terrain[$point->y + 1][$point->x];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x, $point->y - 1));
        }

        // yが偶数 => (+1:-1), (+1:+1)
        if ($point->y % 2 === 0) {
            //
            if ($this->inRange($point->y - 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x + 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->terrain[$point->y - 1][$point->x + 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x + 1, $point->y - 1));
            }
            if ($this->inRange($point->y + 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x + 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->terrain[$point->y + 1][$point->x + 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x + 1, $point->y + 1));
            }
        } else {
            // yが偶数 => (-1:-1), (-1:+1)
            if ($this->inRange($point->y - 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x - 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->terrain[$point->y - 1][$point->x - 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x - 1, $point->y - 1));
            }
            if ($this->inRange($point->y + 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x - 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->terrain[$point->y + 1][$point->x - 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x - 1, $point->y + 1));
            }
        }

        if ($range === 1) {
            return $cells;
        }

        for ($r = 1; $r < $range; $r++) {
            foreach ($cells as $cell) {
                $aroundCells = $this->getAroundCells($cell->getPoint(), 1, $includeOutOfRegion)->reject(function ($cell) use ($point) {
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

    public function replaceShallowToLake(): static
    {
        $isChecked = new Collection();
        $lakeCandidate = new Collection();

        /** @var Cell $cell */
        foreach ($this->terrain->flatten(1) as $cell) {
            if ($cell::TYPE === Lake::TYPE) {
                $this->setCell($cell->getPoint(), new Shallow(point: $cell->getPoint()));
            } else if ($cell::ATTRIBUTE[CellTypeConst::IS_LAND]) {
                $isChecked[$cell->getPoint()->toString()] = true;
                continue;
            }

            if ($cell->getPoint()->x === 0 || $cell->getPoint()->x === \HakoniwaService::getMaxWidth() - 1 ||
                $cell->getPoint()->y === 0 || $cell->getPoint()->y === \HakoniwaService::getMaxHeight() - 1) {
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
            foreach ($cells as $cell) {
                // 走査済み
                if ($isChecked[$cell->getPoint()->toString()]) {
                    continue;
                }
                // 未走査
                $lakeCandidate->push($cell->getPoint());
                $isChecked[$cell->getPoint()->toString()] = true;
            }
        }

        $isLake = $isChecked->reject(function ($val) {
            return $val;
        });

        $isLake->each(function ($val, $key) {
            $point = Point::fromString($key);
            $this->setCell($point, new Lake(point: $point));
        });

        return $this;
    }

    public function occurDisaster(Island $island, Status $status, Turn $turn): DisasterResult
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

    public function findByTypes(array $cellTypes): Collection
    {
        return $this->terrain->flatten(1)->filter(function ($cell) use ($cellTypes) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, $cellTypes, true);
        });
    }

    public function findByAttribute(string $attribute): Collection
    {
        return $this->terrain->flatten(1)->filter(function ($cell) use ($attribute) {
            /** @var Cell $cell */
            return $cell::ATTRIBUTE[$attribute];
        });
    }

    public function inviteNewImmigration(Status $status): void
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() / 2);
        /** @var Cell $targetCell */
        $plains = $this->findByTypes([Plain::TYPE, Wasteland::TYPE]);
        if (!$plains->isEmpty()) {
            $targetCell = $plains->random();
            $this->setCell($targetCell->getPoint(), new Village(point: $targetCell->getPoint()));
            return;
        }

        $shallows = $this->findByTypes([Shallow::TYPE, Mountain::TYPE]);
        if (!$shallows->isEmpty()) {
            $targetCell = $shallows->random();
            $this->setCell($targetCell->getPoint(), new Village(point: $targetCell->getPoint()));
            return;
        }

        $targetCell = $this->terrain->flatten(1)->random();
        $this->setCell($targetCell->getPoint(), new Village(point: $targetCell->getPoint()));
    }
}
