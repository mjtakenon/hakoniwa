<?php

namespace App\Entity\Edge\Others;

use App\Entity\Cell\PassTurnResult;
use App\Entity\Edge\Edge;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Shallow extends Edge
{
    public const TYPE = 'shallow';

    protected string $type = self::TYPE;
}
