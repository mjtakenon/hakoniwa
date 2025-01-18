<?php

namespace App\Entity\Edge\Others;

use App\Entity\Edge\Edge;

class WaterSource extends Edge
{
    public const TYPE = 'water_source';

    protected string $type = self::TYPE;
}
