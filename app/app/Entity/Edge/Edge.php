<?php

namespace App\Entity\Edge;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\OutOfRegion;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Edge\Others\Plain;
use App\Entity\Edge\Others\Shore;
use App\Entity\Edge\Others\Wasteland;
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

    public const ELEVATION = CellConst::ELEVATION_LAND;

    protected string $name;
    protected string $type;
    protected Point $point;
    protected int $face;

    protected int $elevation = self::ELEVATION;

    public function __construct(...$data)
    {
        $this->point = new Point($data['point']->x, $data['point']->y);
        $this->face = $data['face'];
        $this->elevation = $data['elevation'];
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
                'elevation' => $this->getElevation(),
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
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y-1), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 1) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y-1), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 2) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 3) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 4) {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y+1), elevation: CellConst::ELEVATION_MIN);
                }
            } else {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y+1), elevation: CellConst::ELEVATION_MIN);
                }
            }
        } else {
            if ($this->getFace() === 0) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y-1), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 1) {
                if ($this->inRange($this->point->y-1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y-1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y-1), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 2) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 3) {
                if ($this->inRange($this->point->y, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x+1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x+1, $this->point->y));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x+1, $this->point->y), elevation: CellConst::ELEVATION_MIN);
                }
            } else if ($this->getFace() === 4) {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x-1, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x-1, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x-1, $this->point->y+1), elevation: CellConst::ELEVATION_MIN);
                }
            } else {
                if ($this->inRange($this->point->y+1, 0, \HakoniwaService::getMaxHeight()) && $this->inRange($this->point->x, 0, \HakoniwaService::getMaxWidth())) {
                    $cells[] = $terrain->getCell(new Point($this->point->x, $this->point->y+1));
                } else {
                    $cells[] = new OutOfRegion(point: new Point($this->point->x, $this->point->y+1), elevation: CellConst::ELEVATION_MIN);
                }
            }
        }
        return $cells;
    }

    static public function fromJson(string $type, $data): Edge
    {
        return EdgeConst::getClassByType($type, $data);
    }

    public function weathering(Terrain $terrain): Terrain
    {
        /** @var Collection<Cell> $cells */
        $cells = $this->getAdjacentCells($terrain);

        /** @var Cell $cell1 */
        $cell1 = $cells[0];
        /** @var Cell $cell2 */
        $cell2 = $cells[1];

        $elevation1 = $cell1->getElevation();
        $elevation2 = $cell2->getElevation();
        $avr = ($elevation1 + $elevation2) / 2;

        if ($avr < CellConst::ELEVATION_SHALLOW) {
            // 平均が-2以下: 海
            $terrain->setEdge(EdgeConst::getDefaultEdge($this->point, $this->face, $avr));
        } else if ($avr < CellConst::ELEVATION_LAND) {
            // -2~0: 浅瀬（陸地に面していたら砂浜）
            if (($elevation1 >= CellConst::ELEVATION_LAND || $elevation2 >= CellConst::ELEVATION_LAND) && $avr > CellConst::ELEVATION_SHALLOW) {
                $terrain->setEdge(new Shore(point: $this->point, face: $this->face, elevation: 0));
            } else {
                $terrain->setEdge(EdgeConst::getDefaultEdge($this->point, $this->face, $avr));
            }
        } else {
            // 0~: 両方陸地であれば
            // TODO: 実装する
            if ($cell1->getType() === Wasteland::TYPE || $cell2->getType() === Wasteland::TYPE) {
                $terrain->setEdge(new Wasteland(point: $this->point, face: $this->face, elevation: $avr));
            } else {
                $terrain->setEdge(new Plain(point: $this->point, face: $this->face, elevation: $avr));
            }
        }
        return $terrain;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $terrain = $this->weathering($terrain);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
