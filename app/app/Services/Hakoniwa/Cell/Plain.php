<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\HasPopulation\City;
use App\Services\Hakoniwa\Cell\HasPopulation\Metropolis;
use App\Services\Hakoniwa\Cell\HasPopulation\Town;
use App\Services\Hakoniwa\Cell\HasPopulation\Village;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;

class Plain extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land2.gif';
    public const TYPE = 'plain';
    public const NAME = '平地';
    public const IMMIGRATE_COEF = 0.2;
    public const IMMIGRABLE_TYPE = [
        Metropolis::TYPE,
        City::TYPE,
        Town::TYPE,
        Village::TYPE,
        Farm::TYPE,
    ];

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
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
