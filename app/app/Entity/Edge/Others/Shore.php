<?php

namespace App\Entity\Edge\Others;

use App\Entity\Edge\Edge;

class Shore extends Edge
{
    public const TYPE = 'shore';

    protected string $type = self::TYPE;
}
