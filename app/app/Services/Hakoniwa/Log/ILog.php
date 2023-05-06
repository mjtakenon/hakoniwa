<?php

namespace App\Services\Hakoniwa\Log;

interface ILog
{
    public function generate(): string;
    public function getVisibility(): string;
}
