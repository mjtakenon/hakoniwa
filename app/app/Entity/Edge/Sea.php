<?php

namespace App\Entity\Edge;

class Sea extends Edge
{
    public const TYPE = 'sea';

    protected string $type = self::TYPE;
}
