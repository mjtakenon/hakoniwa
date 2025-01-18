<?php

namespace App\Entity\Edge\Others;

use App\Entity\Edge\Edge;

class River extends Edge
{
    public const TYPE = 'river';

    protected string $type = self::TYPE;
}
