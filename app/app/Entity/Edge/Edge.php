<?php

namespace App\Entity\Edge;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\OutOfRegion;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Edge\Others\Shore;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Entity\Util\Range;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

abstract class Edge
{
    use Range;

    public const ELEVATION = CellConst::ELEVATION_PLAIN;

    protected string $name;
    protected string $type;
    protected Point $point;
    protected int $face;

    protected int $elevation = self::ELEVATION;

    public function __construct(...$data)
    {
        $this->point = new Point($data['point']->x, $data['point']->y);
        $this->face = $data['face'];
    }

    public static function create($data)
    {
        return new static($data);
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'data' => [
                'point' => $this->getPoint(),
                'face' => $this->getFace(),
            ]
        ];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getFace(): int
    {
        return $this->face;
    }

    public function getElevation(): int
    {
        return $this->elevation;
    }

    // 隣接するセルを取得
    public function getAdjacentCells(Terrain $terrain): Collection
    {
        $cells = collect([$terrain->getCell($this->point)]);
        if ($this->point->y % 2 === 0) {
            if ($this->getFace() === 0) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y-1));
                }
            } else if ($this->getFace() === 1) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y-1));
                }
            } else if ($this->getFace() === 2) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y));
                }
            } else if ($this->getFace() === 3) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y));
                }
            } else if ($this->getFace() === 4) {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y+1));
                }
            } else {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y+1));
                }
            }
        } else {
            if ($this->getFace() === 0) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y-1));
                }
            } else if ($this->getFace() === 1) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y-1));
                }
            } else if ($this->getFace() === 2) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y));
                }
            } else if ($this->getFace() === 3) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y));
                }
            } else if ($this->getFace() === 4) {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y+1));
                }
            } else {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y+1));
                }
            }
        }
        return $cells;
    }

    static public function fromJson(string $type, $data): Edge
    {
        return EdgeConst::getClassByType($type, $data);
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        /** @var Collection<Cell> $cells */
        $cells = $this->getAdjacentCells($terrain);

        $elevation1 = $cells[0]->getElevation();
        $elevation2 = $cells[1]->getElevation();

        if ($elevation1 === $elevation2) {
            if ($elevation1 === CellConst::ELEVATION_SEA && $elevation2 === CellConst::ELEVATION_SEA) {
                $terrain->setEdge($this->point, EdgeConst::getDefaultEdge($this->point, $this->face, -2));
            } else if ($elevation1 === CellConst::ELEVATION_SHALLOW && $elevation2 === CellConst::ELEVATION_SHALLOW) {
                $terrain->setEdge($this->point, EdgeConst::getDefaultEdge($this->point, $this->face, -1));
            } else if ($elevation1 === CellConst::ELEVATION_PLAIN && $elevation2 === CellConst::ELEVATION_PLAIN) {
                $terrain->setEdge($this->point, EdgeConst::getDefaultEdge($this->point, $this->face, 0));
            } else if ($elevation1 === CellConst::ELEVATION_MOUNTAIN && $elevation2 === CellConst::ELEVATION_MOUNTAIN) {
                $terrain->setEdge($this->point, EdgeConst::getDefaultEdge($this->point, $this->face, 1));
            }
        } else {
            $avr = ($elevation1 + $elevation2) / 2;
            if ($avr < 0.5) {
                $terrain->setEdge($this->point, EdgeConst::getDefaultEdge($this->point, $this->face, ceil($avr)));
            } else if ($avr === -0.5) {
                $terrain->setEdge($this->point, new Shore(point: $this->point, face: $this->face));
            } else {
                // TODO: 実装する
            }
        }

        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
