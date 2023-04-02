<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

abstract class Cell
{
    protected ?string $imagePath;
    protected ?string $type;
    protected Point $point;

    public function __construct(Point|\stdClass $point)
    {
        $this->point = $point;
        $this->imagePath = null;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,//get_class($this),
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
            ]
        ];
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    static public function fromJson(string $type, Cell|\stdClass $data): Cell
    {
        return new (CellTypeConst::getClassByType($type))(new Point($data->point->x, $data->point->y));
    }

}
