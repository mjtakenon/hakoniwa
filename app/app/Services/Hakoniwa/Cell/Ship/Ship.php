<?php

namespace App\Services\Hakoniwa\Cell\Ship;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

abstract class Ship extends Cell
{
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => false,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::IS_SHIP => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => false,
    ];

    protected string $shallowImagePath;
    protected string $seaImagePath;
    protected int $elevation = -1;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['elevation'] = $this->elevation;
        return $arr;
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('elevation', $data)) {
            $this->elevation = $data['elevation'];
        } else {
            $this->elevation = -1;
        }
    }

    public function getImagePath(): string
    {
        return $this->elevation === -1 ? $this->shallowImagePath : $this->seaImagePath;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $logs = Logs::create();

        $seaCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            /** @var Cell $cell */
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
        });
        /** @var Cell $cell */
        $cell = $seaCells->random();

        $beforePoint = $this->point;
        $this->point = $cell->getPoint();
        $terrain->setCell($this->point, $this);

        if ($this->elevation === -1) {
            $terrain->setCell($beforePoint, new Shallow(point: $beforePoint));
        } else {
            $terrain->setCell($beforePoint, new Sea(point: $beforePoint));
        }

        return new PassTurnResult($terrain, $status, $logs);
    }
}
