<?php

namespace App\Services\Hakoniwa\Log;

use App\Services\Hakoniwa\Util\Point;

interface ILog
{
    public function generate(): string;
    public function getVisibility(): string;
}
