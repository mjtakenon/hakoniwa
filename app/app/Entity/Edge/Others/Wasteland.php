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

class Wasteland extends Edge
{
    public const TYPE = 'wasteland';

    protected string $type = self::TYPE;

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
