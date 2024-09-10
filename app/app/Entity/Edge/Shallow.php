<?php

namespace App\Entity\Edge;

class Shallow extends Edge
{
    public const TYPE = 'shallow';

    protected string $type = self::TYPE;
}
