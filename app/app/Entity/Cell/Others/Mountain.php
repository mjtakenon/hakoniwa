<?php

namespace App\Entity\Cell\Others;

use App\Entity\Cell\Cell;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Cell\ResourcesProduction\Mine;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use App\Entity\Cell\CellConst;

class Mountain extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land11.gif';
    public const TYPE = 'mountain';
    public const NAME = '山';
    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => false,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];
    public const ELEVATION = 1;
    private const ACTIVATE_PROBABILITY = 0.3;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $elevation = self::ELEVATION;

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
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
