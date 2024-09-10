<?php

namespace App\Entity\Edge;

class Plain extends Edge
{
    public const TYPE = 'plain';

    protected string $type = self::TYPE;
}
