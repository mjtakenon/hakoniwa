<?php

namespace App\Services\Hakoniwa\Util;

class Rand
{
    public static function mt_rand_float(): float
    {
        return (float)mt_rand() / mt_getrandmax();
    }
}
