<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\IslandStatus;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

abstract class Cell
{
    const IMAGE_PATH = '';
    const TYPE = '';
    const NAME = '';

    protected ?string $imagePath;
    protected ?string $type;
    protected Point $point;

    protected int $population;


    public function __construct(Point|\stdClass $point)
    {
        $this->point = $point;
        $this->imagePath = null;
        $this->population = 0;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,//get_class($this),
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
            ]
        ];
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInfoString(): string
    {
        return '';
    }

    public function getPopulation(): int {
        return $this->population;
    }

    static public function fromJson(string $type, Cell|\stdClass $data): Cell
    {
        return new (CellTypeConst::getClassByType($type))(new Point($data->point->x, $data->point->y));
    }

//    islandとstatusはモデルと別クラスに切り出す?
//    public function elapse(Island $island, Status $status, Terrain $terrain): Cell
//    {
//        return new $this;
//    }
}
