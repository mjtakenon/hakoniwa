<?php

namespace App\Entity\Util;

trait Range
{
    private function inRange(int $n, int $min, int $max): bool
    {
        return $n >= $min && $n < $max;
    }
}
