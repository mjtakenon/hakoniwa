<?php

namespace App\Entity\Util;

class Point
{
    public $x;
    public $y;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function toString(): string
    {
        return $this->x . ',' . $this->y;
    }

    public static function fromString(string $point): static
    {
        $arr = explode(',', $point);
        return new static((int)$arr[0], (int)$arr[1]);
    }
}
