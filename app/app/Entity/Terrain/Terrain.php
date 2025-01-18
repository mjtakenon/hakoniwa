<?php

namespace App\Entity\Terrain;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\FoodsProduction\IFoodsProduction;
use App\Entity\Cell\FundsProduction\Factory;
use App\Entity\Cell\FundsProduction\IFundsProduction;
use App\Entity\Cell\FundsProduction\LargeFactory;
use App\Entity\Cell\HasPopulation\IHasPopulation;
use App\Entity\Cell\HasPopulation\Village;
use App\Entity\Cell\HasWoods\Forest;
use App\Entity\Cell\IHasMaintenanceNumberOfPeople;
use App\Entity\Cell\MaintenanceInfo;
use App\Entity\Cell\Others\Lake;
use App\Entity\Cell\Others\Mountain;
use App\Entity\Cell\Others\OutOfRegion;
use App\Entity\Cell\Others\Plain;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Volcano;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Cell\ResourcesProduction\IResourcesProduction;
use App\Entity\Cell\ResourcesProduction\Oilfield;
use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\CombatantShip;
use App\Entity\Cell\Ship\Submarine;
use App\Entity\Edge\Edge;
use App\Entity\Edge\EdgeConst;
use App\Entity\Edge\Others\River;
use App\Entity\Edge\Others\WaterSource;
use App\Entity\JsonCodable;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Util\Normal;
use App\Entity\Util\Point;
use App\Entity\Util\Rand;
use App\Entity\Util\Range;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class Terrain implements JsonCodable
{
    use Range;

    public const AREA_PER_CELL = 100;
    private Collection $cells;
    private Collection $edges;

    public function __construct()
    {
        $cells = new Collection();
        $edges = new Collection();

        for ($y = 0; $y < \HakoniwaService::getMaxHeight(); $y++) {
            $cellsRow = new Collection();
            $edgesRow = new Collection();
            for ($x = 0; $x < \HakoniwaService::getMaxWidth(); $x++) {
                $cellsRow[] = new \App\Entity\Cell\Others\Sea(point: new Point($x, $y), elevation: CellConst::ELEVATION_SEA);
                $edgesColumn = new Collection();
                for ($face = 0; $face < 3; $face++) {
                    if (!$this->hasEdge(new Point($x, $y), $face)) {
                        continue;
                    }
                    $edgesColumn[] = new \App\Entity\Edge\Others\Sea(point: new Point($x, $y), face: $face, elevation: CellConst::ELEVATION_SEA);
                }
                $edgesRow[] = $edgesColumn;
            }
            $cells[] = $cellsRow;
            $edges[] = $edgesRow;
        }

        $this->cells = $cells;
        $this->edges = $edges;
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
        return [
            'cells' => $this->cells->flatten()->map(function (Cell $cell) use ($isPrivate, $withStatic) {
                return $cell->toArray($isPrivate, $withStatic);
            }),
            'edges' => $this->edges->flatten()->map(function (Edge $edge) {
                return $edge->toArray();
            }),
        ];
    }

    public static function fromJson(string $json): Terrain
    {
        $objects = json_decode($json);

        $cells = new Collection();
        $edges = new Collection();
        for ($y = 0; $y < \HakoniwaService::getMaxHeight(); $y++) {
            $cellsRow = new Collection();
            $edgesRow = new Collection();
            for ($x = 0; $x < \HakoniwaService::getMaxWidth(); $x++) {
                $cellsRow[] = null;
                $edgesColumn = new Collection();
                for ($face = 0; $face < 3; $face++) {
                    $edgesColumn[] = null;
                }
                $edgesRow[] = $edgesColumn;
            }
            $cells[] = $cellsRow;
            $edges[] = $edgesRow;
        }

        foreach ($objects->cells as $object) {
            $cell = Cell::fromJson($object->type, $object->data);
            $cells[$cell->getPoint()->y][$cell->getPoint()->x] = $cell;
        }

        foreach ($objects->edges as $object) {
            $edge = Edge::fromJson($object->type, $object->data);
            $edges[$edge->getPoint()->y][$edge->getPoint()->x][$edge->getFace()] = $edge;
        }

        // 端のEdgeはnullになるのでArrayから除去
        $edges = $edges->map(function (Collection $y) {
            return $y->map(function (Collection $x) {
                return $x->filter(function (?Edge $e) {
                    return !is_null($e);
                });
            });
        });

        $static = new static();
        $static->setCells($cells);
        $static->setEdges($edges);

        return $static;
    }

    public function init(): Terrain
    {
        $n = 0;
        while (true) {
            $x = (int)Normal::normal(\HakoniwaService::getMaxWidth() / 2, \HakoniwaService::getMaxWidth() / 11);
            $y = (int)Normal::normal(\HakoniwaService::getMaxHeight() / 2, \HakoniwaService::getMaxHeight() / 11);

            if ($this->cells[$y][$x]::TYPE === 'sea') {
                if ($n < 4) {
                    $this->cells[$y][$x] = new Forest(point: new Point($x, $y), elevation: CellConst::ELEVATION_MAX / 3);
                } else if ($n < 18) {
                    $this->cells[$y][$x] = new Wasteland(point: new Point($x, $y), elevation: CellConst::ELEVATION_MAX / 3);
                } else if ($n < 22) {
                    $this->cells[$y][$x] = new Volcano(point: new Point($x, $y), elevation: CellConst::ELEVATION_MAX);
                } else if ($n < 23) {
                    $this->cells[$y][$x] = new Village(point: new Point($x, $y), elevation: CellConst::ELEVATION_MAX / 3, population: 1000);
                } else if ($n < 38) {
                    $this->cells[$y][$x] = new Plain(point: new Point($x, $y), elevation: CellConst::ELEVATION_MAX / 3);
                } else if ($n < 48) {
                    $this->cells[$y][$x] = new Shallow(point: new Point($x, $y), elevation: CellConst::ELEVATION_SHALLOW);
//                } else if ($n < 19) {
//                    $this->cells[$y][$x] = new Volcano(point: new Point($x, $y));
//                } else if ($n < 21) {
//                    $this->cells[$y][$x] = new Village(point: new Point($x, $y), population: 1000);
//                } else if ($n < 28) {
//                    $this->cells[$y][$x] = new Plain(point: new Point($x, $y));
//                } else if ($n < 38) {
//                    $this->cells[$y][$x] = new Shallow(point: new Point($x, $y));
                } else {
                    break;
                }
                $n++;
            }
        }

        // Cellの平坦化
        $terrainTmp = deep_copy($this);
        $this->cells->flatten()->each(function (Cell $cell) use ($terrainTmp) {
            $this->setCell($cell->weathering($terrainTmp));
        });

        // Edgeの初期配置
        $this->cells->flatten()->each(function (Cell $cell) {
            for ($face = 0; $face < 3; $face++) {
                if (!$this->hasEdge($cell->getPoint(), $face)) {
                    continue;
                }

                if ($cell->getElevation() >= CellConst::ELEVATION_LAND && $cell->getType() !== Wasteland::TYPE) {
                    $this->edges[$cell->getPoint()->y][$cell->getPoint()->x][$face] = new \App\Entity\Edge\Others\Plain(point: new Point($cell->getPoint()->x, $cell->getPoint()->y), face: $face, elevation: CellConst::ELEVATION_LAND);
                } else {
                    $this->edges[$cell->getPoint()->y][$cell->getPoint()->x][$face] = EdgeConst::getDefaultEdge(new Point($cell->getPoint()->x, $cell->getPoint()->y), $face, $cell->getElevation());
                }
            }
        });

        // Edgeの平坦化
        /** @var Collection $edge */
        foreach ($this->edges->flatten(1) as $edges) {
            /** @var Edge $edge */
            foreach ($edges as $edge) {
                $terrain = $edge->weathering($this);
                $this->setEdge($terrain->getEdge(new Point($edge->getPoint()->x, $edge->getPoint()->y), $edge->getFace()));
            }
        }

        return $this->replaceShallowToLake();
    }

    public function getCells(): Collection
    {
        return $this->cells;
    }

    public function getEdges(): Collection
    {
        return $this->edges;
    }

    public function setCells(Collection $cells): void
    {
        $this->cells = $cells;
    }

    public function setEdges(Collection $edges): void
    {
        $this->edges = $edges;
    }

    public function getCell(Point $point): Cell
    {
        return $this->cells[$point->y][$point->x];
    }

    public function setCell(Cell $cell, ?Point $point = null): void
    {
        if (is_null($point)) {
            $this->cells[$cell->getPoint()->y][$cell->getPoint()->x] = $cell;
        } else {
            $this->cells[$point->y][$point->x] = $point;
        }
    }

    public function getEdge(Point $point, int $face): Edge
    {
        return $this->edges[$point->y][$point->x][$face];
    }

    public function setEdge(Edge $edge, ?Point $point = null): void
    {
        if (is_null($point)) {
            $this->edges[$edge->getPoint()->y][$edge->getPoint()->x][$edge->getFace()] = $edge;
        } else {
            $this->edges[$point->y][$point->x][$edge->getFace()] = $edge;
        }
    }

    public function isExistsCell(Point $point): bool
    {
        try {
            $this->cells[$point->y][$point->x];
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function isExistsEdge(Point $point, int $edge): bool
    {
        try {
            $this->edges[$point->y][$point->x][$edge];
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function aggregatePopulation(): int
    {
        return $this->findByInterface(IHasPopulation::class)->sum(function ($cell) {
            /** @var IHasPopulation $cell */
            return $cell->getPopulation();
        });
    }

    public function aggregateFundsProductionCapacity(): int
    {
        return $this->findByInterface(IFundsProduction::class)->sum(function ($cell) {
            /** @var IFundsProduction $cell */
            return $cell->getFundsProductionCapacity();
        });
    }

    public function aggregateFoodsProductionCapacity(): int
    {
        return $this->findByInterface(IFoodsProduction::class)->sum(function ($cell) {
            /** @var IFoodsProduction $cell */
            return $cell->getFoodsProductionCapacity();
        });
    }

    public function aggregateResourcesProductionCapacity(): int
    {
        return $this->findByInterface(IResourcesProduction::class)->sum(function ($cell) {
            /** @var IResourcesProduction $cell */
            return $cell->getResourcesProductionCapacity();
        });
    }

    public function aggregateMaintenanceNumberOfPeople(Island $island, Collection $maintenanceNumberOfPeoples): void
    {
        $maintenanceInfos = $this->findByInterface(IHasMaintenanceNumberOfPeople::class)->map(function ($cell) use ($island) {
            /** @var IHasMaintenanceNumberOfPeople $cell */
            return $cell->getMaintenanceNumberOfPeople($island);
        });

        $maintenanceInfos->groupBy(function ($maintenanceInfo) {
            /** @var MaintenanceInfo $maintenanceInfo */
            return $maintenanceInfo->getAffiliationId();
        })->each(function ($groupedMaintenanceInfo, $index) use ($maintenanceNumberOfPeoples) {
            $sumMaintenanceNumberOfPeoples = $maintenanceNumberOfPeoples->get($index) + $groupedMaintenanceInfo->sum(function ($maintenanceInfo) {
                    /** @var MaintenanceInfo $maintenanceInfo */
                    return $maintenanceInfo->getMaintenanceNumberOfPeople();
                });
            $maintenanceNumberOfPeoples->put($index, $sumMaintenanceNumberOfPeoples);
        });
    }

    public function getEnvironment(): string
    {
        $hasFactory = $this->findByTypes([Factory::TYPE, LargeFactory::TYPE])->isNotEmpty();
        $hasOilField = $this->findByTypes([Oilfield::TYPE])->isNotEmpty();

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
        $landCells = $this->findByAttribute(CellConst::IS_LAND)->count();
        return $landCells * self::AREA_PER_CELL;
    }

    public function passTurn(Island $island, Status $status, Turn $turn, Collection $faceoreignIslandEvents): PassTurnResult
    {
        $logs = Logs::create();

        /** @var Cell $cell */
        foreach ($this->cells->flatten(1) as $cell) {

            // 怪獣の移動などで既に行動されている場合、セルのTypeが変わるため処理を行わない
            if ($cell->getType() !== $this->getCell($cell->getPoint())->getType()) {
                continue;
            }

            if ($cell::ATTRIBUTE[CellConst::IS_MONSTER] && (float)config('app.hakoniwa.monster_action_probably') <= Rand::mt_rand_float()) {
                continue;
            }

            $passTurnResult = $cell->passTurn($island, $this, $status, $turn, $faceoreignIslandEvents);

            $this->cells = $passTurnResult->getTerrain()->getCells();
            $status = $passTurnResult->getStatus();
            $logs->merge($passTurnResult->getLogs());
        }

        /** @var Collection $edge */
        foreach ($this->edges->flatten(1) as $edges) {
            /** @var Edge $edge */
            foreach ($edges as $edge) {
                $passTurnResult = $edge->passTurn($island, $this, $status, $turn, $faceoreignIslandEvents);
                $this->edges = $passTurnResult->getTerrain()->getEdges();
                $status = $passTurnResult->getStatus();
                $logs->merge($passTurnResult->getLogs());
            }
        }

        $this->replaceLandToRiver();

        return new PassTurnResult($this, $status, $logs);
    }

    public function getAroundCells(Point $point, int $range = 1, bool $includeOutOfRegion = false): Collection
    {
        $cells = new Collection();

        if ($this->inRange($point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x - 1, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->cells[$point->y][$point->x - 1];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x - 1, $point->y), elevation: CellConst::ELEVATION_MIN);
        }

        if ($this->inRange($point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x + 1, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->cells[$point->y][$point->x + 1];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x + 1, $point->y), elevation: CellConst::ELEVATION_MIN);
        }

        if ($this->inRange($point->y - 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->cells[$point->y - 1][$point->x];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x, $point->y - 1), elevation: CellConst::ELEVATION_MIN);
        }

        if ($this->inRange($point->y + 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x, 0, \HakoniwaService::getMaxWidth())) {
            $cells[] = $this->cells[$point->y + 1][$point->x];
        } else if ($includeOutOfRegion) {
            $cells[] = new OutOfRegion(point: new Point($point->x, $point->y - 1), elevation: CellConst::ELEVATION_MIN);
        }

        // yが偶数 => (+1:-1), (+1:+1)
        if ($point->y % 2 === 0) {
            //
            if ($this->inRange($point->y - 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x + 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->cells[$point->y - 1][$point->x + 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x + 1, $point->y - 1), elevation: CellConst::ELEVATION_MIN);
            }
            if ($this->inRange($point->y + 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x + 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->cells[$point->y + 1][$point->x + 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x + 1, $point->y + 1), elevation: CellConst::ELEVATION_MIN);
            }
        } else {
            // yが偶数 => (-1:-1), (-1:+1)
            if ($this->inRange($point->y - 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x - 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->cells[$point->y - 1][$point->x - 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x - 1, $point->y - 1), elevation: CellConst::ELEVATION_MIN);
            }
            if ($this->inRange($point->y + 1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($point->x - 1, 0, \HakoniwaService::getMaxWidth())) {
                $cells[] = $this->cells[$point->y + 1][$point->x - 1];
            } else if ($includeOutOfRegion) {
                $cells[] = new OutOfRegion(point: new Point($point->x - 1, $point->y + 1), elevation: CellConst::ELEVATION_MIN);
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

    // TODO: 標高ベースの置き換え処理に変更
    public function replaceShallowToLake(): static
    {
        $isChecked = new Collection();
        $lakeCandidate = new Collection();

        /** @var Cell $cell */
        foreach ($this->cells->flatten(1) as $cell) {
            if ($cell::TYPE === Lake::TYPE) {
                $this->setCell(new Shallow(point: $cell->getPoint(), elevation: $cell->getElevation()));
            } else if ($cell::ATTRIBUTE[CellConst::IS_LAND]) {
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
            $this->setCell(new Lake(point: $point, elevation: CellConst::ELEVATION_SHALLOW));
        });

        return $this;
    }

    private function replaceLandToRiver(): void
    {
        $sources = $this->edges->flatten()->filter(function (Edge $edge) {
            return $edge->getType() === WaterSource::TYPE;
        });

        $sources->each(function (Edge $source) {
            $candidates = $source->getAdjacentEdges($this);
            $candidates->filter(function (Edge $e) {
                return in_array($e->getType(), [\App\Entity\Edge\Others\Sea::TYPE, \App\Entity\Edge\Others\Shallow::TYPE], true);
            });

            // 隣接Edgeが4つでない場合, 海系地形が含まれる場合は端であるため終了
            if ($candidates->count() !== 4) {
                return;
            }

            // とりあえず水源より標高の低いEdgeをフィルタ
            $candidates = $candidates->filter(function (Edge $e) use ($source) {
                return $e->getElevation() <= $source->getElevation();
            });

            if ($candidates->isEmpty()) {
                return;
            }

            // その中で最も標高の低いEdgeを候補とする
            $minElevation = $candidates->min(function (Edge $e) {
                return $e->getElevation();
            });

            /** @var Edge $candidate */
            $candidate = $candidates->first(function (Edge $e) use ($minElevation) {
                return $e->getElevation() <= $minElevation;
            });

            $this->setEdge(new River(point: $candidate->getPoint(), face: $candidate->getFace(), elevation: $candidate->getElevation()));
        });
    }

    public function findByTypes(array $cellTypes): Collection
    {
        return $this->cells->flatten(1)->filter(function ($cell) use ($cellTypes) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, $cellTypes, true);
        });
    }

    public function findByAttribute(string $attribute): Collection
    {
        return $this->cells->flatten(1)->filter(function ($cell) use ($attribute) {
            /** @var Cell $cell */
            return $cell::ATTRIBUTE[$attribute];
        });
    }

    public function findByInterface(string $interface): Collection
    {
        return $this->cells->flatten(1)->filter(function ($cell) use ($interface) {
            /** @var Cell $cell */
            return array_key_exists($interface, class_implements($cell));
        });
    }

    public function inviteNewImmigration(Status $status): void
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() / 2);
        /** @var Cell $targetCell */
        $plains = $this->findByTypes([Plain::TYPE, Wasteland::TYPE]);
        if (!$plains->isEmpty()) {
            $targetCell = $plains->random();
            $this->setCell(new Village(point: $targetCell->getPoint(), elevation: $targetCell->getElevation()));
            return;
        }

        $shallows = $this->findByTypes([Shallow::TYPE, Mountain::TYPE]);
        if (!$shallows->isEmpty()) {
            $targetCell = $shallows->random();
            $this->setCell(new Village(point: $targetCell->getPoint(), elevation: $targetCell->getElevation()));
            return;
        }

        $targetCell = $this->cells->flatten(1)->random();
        $this->setCell(new Village(point: $targetCell->getPoint(), elevation: $targetCell->getElevation()));
    }

    public function changeIslandName(Island $island): static
    {
        // TODO: 調査船ができたら追加
        $ships = $this->findByTypes([Battleship::TYPE, Submarine::TYPE]);

        /** @var CombatantShip $ship */
        foreach ($ships as $ship) {
            if ($ship->getAffiliationId() === $island->id) {
                $ship->setAffiliationName($island->name);
                $this->setCell($ship);
            }
        }

        return $this;
    }

    // その箇所のEdgeを設置するか
    private function hasEdge(Point $point, int $face): bool
    {
        // 端にはEdgeを設置しない
        return !(
            ($point->y === 0 && ($face === 0 || $face === 1)) ||
            ($point->x === 0 && ($face === 2 || $point->y % 2 === 1 && $face === 0)) ||
            ($point->x === \HakoniwaService::getMaxWidth() - 1 && $point->y % 2 === 0 && $face === 1));
    }
}
