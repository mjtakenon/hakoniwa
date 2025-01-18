<?php

namespace App\Entity\Edge;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\HasWoods\Forest;
use App\Entity\Cell\Others\OutOfRegion;
use App\Entity\Cell\Others\Volcano;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Edge\Others\Plain;
use App\Entity\Edge\Others\River;
use App\Entity\Edge\Others\Shore;
use App\Entity\Edge\Others\Wasteland;
use App\Entity\Edge\Others\WaterSource;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Entity\Util\Range;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

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
        $adjacentPoint = deep_copy($this->point);

        if ($this->point->y % 2 === 0) {
            if ($this->face % 2 === 1) {
                $adjacentPoint->x += 1;
            } else if ($this->face === 2) {
                $adjacentPoint->x -= 1;
            }
        } else {
            if ($this->face % 2 === 0) {
                $adjacentPoint->x -= 1;
            } else if ($this->face === 3) {
                $adjacentPoint->x += 1;
            }
        }

        if ($this->face <= 1) {
            $adjacentPoint->y -= 1;
        } else if ($this->face >= 4) {
            $adjacentPoint->y += 1;
        }

        if ($terrain->isExistsCell($adjacentPoint)) {
            $cells[] = $terrain->getCell($adjacentPoint);
        } else {
            $cells[] = new OutOfRegion(point: $adjacentPoint, elevation: CellConst::ELEVATION_MIN);
        }

        return $cells;
    }

    // 隣接するEdgeを取得
    public function getAdjacentEdges(Terrain $terrain): Collection
    {
        $edges = collect();

        if ($this->getFace() === 0) {
            if ($terrain->isExistsEdge(new Point($this->point->x-1, $this->point->y), 1)) {
                $edges[] = $terrain->getEdge(new Point($this->point->x-1, $this->point->y), 1);
            }
            if ($terrain->isExistsEdge($this->point, 2)) {
                $edges[] = $terrain->getEdge($this->point, 2);
            }
            if ($this->point->y % 2 === 0) {
                if ($terrain->isExistsEdge(new Point($this->point->x+1, $this->point->y-1), 2)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x+1, $this->point->y-1), 2);
                }
            } else {
                if ($terrain->isExistsEdge(new Point($this->point->x, $this->point->y-1), 2)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x, $this->point->y-1), 2);
                }
            }
            if ($terrain->isExistsEdge($this->point, 1)) {
                $edges[] = $terrain->getEdge($this->point, 1);
            }
        } else if ($this->getFace() === 1) {
            if ($terrain->isExistsEdge($this->point, 0)) {
                $edges[] = $terrain->getEdge($this->point, 0);
            }
            if ($this->point->y % 2 === 0) {
                if ($terrain->isExistsEdge(new Point($this->point->x+1, $this->point->y-1), 2)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x+1, $this->point->y-1), 2);
                }
            } else {
                if ($terrain->isExistsEdge(new Point($this->point->x, $this->point->y-1), 2)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x, $this->point->y-1), 2);
                }
            }
            if ($terrain->isExistsEdge(new Point($this->point->x+1, $this->point->y), 0)) {
                $edges[] = $terrain->getEdge(new Point($this->point->x+1, $this->point->y), 0);
            }
            if ($terrain->isExistsEdge(new Point($this->point->x+1, $this->point->y), 2)) {
                $edges[] = $terrain->getEdge(new Point($this->point->x+1, $this->point->y), 2);
            }
        } else {
            if ($terrain->isExistsEdge($this->point, 0)) {
                $edges[] = $terrain->getEdge($this->point, 0);
            }
            if ($terrain->isExistsEdge(new Point($this->point->x-1, $this->point->y), 1)) {
                $edges[] = $terrain->getEdge(new Point($this->point->x-1, $this->point->y), 1);
            }
            if ($this->point->y % 2 === 0) {
                if ($terrain->isExistsEdge(new Point($this->point->x, $this->point->y+1), 0)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x, $this->point->y+1), 0);
                }
                if ($terrain->isExistsEdge(new Point($this->point->x, $this->point->y+1), 1)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x, $this->point->y+1), 1);
                }
            } else {
                if ($terrain->isExistsEdge(new Point($this->point->x-1, $this->point->y+1), 0)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x-1, $this->point->y+1), 0);
                }
                if ($terrain->isExistsEdge(new Point($this->point->x-1, $this->point->y+1), 1)) {
                    $edges[] = $terrain->getEdge(new Point($this->point->x-1, $this->point->y+1), 1);
                }
            }
        }
        return $edges;
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
            if (in_array($cell1->getType(), [Wasteland::TYPE, Volcano::TYPE], true) || in_array($cell2->getType(), [Wasteland::TYPE, Volcano::TYPE], true)) {
                $terrain->setEdge(new Wasteland(point: $this->point, face: $this->face, elevation: $avr));
            } else {
                $terrain->setEdge(new Plain(point: $this->point, face: $this->face, elevation: $avr));
            }
        }
        return $terrain;
    }

    public function setRiverSource(Terrain $terrain) : Terrain
    {
        /** @var Collection<Cell> $cells */
        $cells = $this->getAdjacentCells($terrain);

        /** @var Cell $cell1 */
        $cell1 = $cells[0];
        /** @var Cell $cell2 */
        $cell2 = $cells[1];

        if ((in_array($cell1->getType(), [Forest::TYPE], true) || $cell1::ATTRIBUTE[CellConst::IS_MOUNTAIN]) && (in_array($cell2->getType(), [Forest::TYPE], true) || $cell2::ATTRIBUTE[CellConst::IS_MOUNTAIN])) {
            $terrain->setEdge(new WaterSource(point: $this->point, face: $this->face, elevation: $this->elevation));
        }
        return $terrain;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $terrain = $this->weathering($terrain);
        $terrain = $this->setRiverSource($terrain);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
