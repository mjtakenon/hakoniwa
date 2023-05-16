<?php

namespace App\Entity\Log;

abstract class LogRow
{
    abstract public function generate(): string;
    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
