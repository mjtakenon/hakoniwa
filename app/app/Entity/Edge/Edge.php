<?php

namespace App\Entity\Edge;

use App\Entity\Cell\CellConst;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Point;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

abstract class Edge
{
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
//    public function getAdjacentCells(Terrain $terrain): Collection
//    {
//        $aroundCells = $terrain->getAroundCells($this->point, includeOutOfRegion: true);
//
//        if ($this->point->y % 2 === 0) {
//            return match ($this->getFace()) {
//                0 => collect([$terrain->getCell($this->point), $terrain->getCell($this->point->x)]
//            };
//        } else {
//
//        }
//    }

    static public function fromJson(string $type, $data): Edge
    {
        return EdgeConst::getClassByType($type, $data);
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
