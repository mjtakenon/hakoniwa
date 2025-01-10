<?php

namespace App\Entity\Cell\Others;

use App\Entity\Cell\Cell;
use App\Entity\Cell\FoodsProduction\Farm;
use App\Entity\Cell\FoodsProduction\FarmDome;
use App\Entity\Cell\HasPopulation\City;
use App\Entity\Cell\HasPopulation\Metropolis;
use App\Entity\Cell\HasPopulation\Town;
use App\Entity\Cell\HasPopulation\Village;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use App\Entity\Cell\CellConst;

class Plain extends Cell
{
    public const TYPE = 'plain';
    public const NAME = '平地';
    public const IMMIGRATE_COEF = 0.2;
    public const IMMIGRABLE_TYPE = [
        Metropolis::TYPE,
        City::TYPE,
        Town::TYPE,
        Village::TYPE,
        Farm::TYPE,
        FarmDome::TYPE,
    ];

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
        CellConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $cells = $terrain->getAroundCells($this->point);
        $immigrableCells = $cells->filter(function ($cell) {
            return in_array($cell::TYPE, self::IMMIGRABLE_TYPE, true);
        });
        if ($immigrableCells->count() * self::IMMIGRATE_COEF > Rand::mt_rand_float()) {
            $terrain->setCell($this->point, new Village(point: $this->point));
        }
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
