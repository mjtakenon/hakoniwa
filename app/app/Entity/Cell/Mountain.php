<?php

namespace App\Entity\Cell;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Mountain extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land11.gif';
    public const TYPE = 'mountain';
    public const NAME = '山';
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::IS_SHIP => false,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => false,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];
    public const ELEVATION = 1;
    private const ACTIVATE_PROBABILITY = 0.3;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $elevation = self::ELEVATION;

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $mountains = $terrain->findByTypes([Volcano::TYPE, Mine::TYPE]);

        if ($mountains->count() >= 1) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        // 火山が存在していない場合、確率で火山になる
        if (self::ACTIVATE_PROBABILITY <= Rand::mt_rand_float()) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        $terrain->setCell($this->point, new Volcano(point: $this->point));

        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
