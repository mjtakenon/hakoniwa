<?php

namespace App\Entity\Achievement;

use App\Models\Turn;

abstract class Achievement
{
    protected Turn $turn;

    public function __construct(Turn $turn)
    {
        $this->turn = $turn;
    }

    abstract public function getType(): string;
    abstract public function getName(): string;

    public function getExtra(): string
    {
        return '';
    }
}
