<?php

namespace App\Entity\Cell\Others;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Monster\Begenoth;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\LogRow\HatchLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Egg extends Cell
{
    public const TYPE = 'egg';
    public const NAME = '卵';
    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::IS_MOUNTAIN => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    private const HATCH_PROBABILITY = 0.2;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $logs = Logs::create();

        if (self::HATCH_PROBABILITY <= Rand::mt_rand_float()) {
            return new PassTurnResult($terrain, $status, $logs);
        }

        $monster = new Begenoth(point: $this->point, elevation: $this->elevation, remain_move_times: 0);
        $terrain->setCell($monster, $this->point);
        $logs->add(new HatchLog($island, $this, $monster));

        return new PassTurnResult($terrain, $status, $logs);
    }
}
