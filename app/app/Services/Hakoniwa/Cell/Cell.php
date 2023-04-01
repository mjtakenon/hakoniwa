<?php

namespace App\Services\Hakoniwa\Cell;

abstract class Cell
{
    public const CELL_SEA = 'sea';
    public const CELL_SHALLOW_WATER = 'shallow_water';
    public const CELL_PLAIN = 'plain';
    public const CELL_MOUNTAIN = 'mountain';
    protected int $x;
    protected int $y;
    protected string $imagePath;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}
