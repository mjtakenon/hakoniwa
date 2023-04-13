<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class Plain extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land2.gif';
    public const TYPE = 'plain';
    public const NAME = '平地';
    public const IMMIGRATE_COEF = 0.2;
    public const IMMIGRABLE_TYPE = [
        City::TYPE,
        Town::TYPE,
        Village::TYPE,
        Farm::TYPE,
    ];

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME;
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $cells = $terrain->getAroundCells($this->point);
        $immigrableCells = $cells->filter(function ($cell) { return in_array($cell::TYPE, self::IMMIGRABLE_TYPE); });
        if ($immigrableCells->count() * self::IMMIGRATE_COEF * 100 >= rand(0, 100)) {
            $terrain->setCell($this->point, new Village(point:$this->point));
        }
    }
}
