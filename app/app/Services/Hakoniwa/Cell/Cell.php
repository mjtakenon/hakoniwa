<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

abstract class Cell
{
    public const CELL_SEA = 'sea';
    public const CELL_SHALLOW_WATER = 'shallow_water';
    public const CELL_PLAIN = 'plain';
    public const CELL_MOUNTAIN = 'mountain';
    protected Point $point;

    public function __construct(Point|\stdClass $point)
    {
        $this->point = $point;
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'data' => [
                'point' => $this->point,
            ]
        ];
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    static public function fromJson(string $class, Cell|\stdClass $data): Cell
    {
        return new $class(new Point($data->point->x, $data->point->y));
    }

}
