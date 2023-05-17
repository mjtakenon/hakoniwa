<?php

namespace App\Entity\Cell\Ship;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellTypeConst;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

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

    protected function move(Terrain $terrain, Cell $beforeCell, Cell $afterCell): Terrain
    {
        /** @var Ship $beforeCellCopy */
        $beforeCellCopy = deep_copy($beforeCell);
        $beforeCell->point = $afterCell->getPoint();
        $beforeCell->elevation = $afterCell->getElevation();
        $terrain->setCell($beforeCell->point, $beforeCell);

        if ($beforeCellCopy->getElevation() === -1) {
            $terrain->setCell($beforeCellCopy->getPoint(), new Shallow(point: $beforeCellCopy->getPoint()));
        } else {
            $terrain->setCell($beforeCellCopy->getPoint(), new Sea(point: $beforeCellCopy->getPoint()));
        }

        return $terrain;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $logs = Logs::create();

        $seaCells = $terrain->findByTypes([Sea::TYPE, Shallow::TYPE]);

        if ($seaCells->count() <= 0) {
            return new PassTurnResult($terrain, $status, $logs);
        }

        /** @var Cell $cell */
        $terrain = $this->move($terrain, $this, $seaCells->random());

        return new PassTurnResult($terrain, $status, $logs);
    }
}
