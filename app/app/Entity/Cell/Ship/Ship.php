<?php

namespace App\Entity\Cell\Ship;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\PassTurnResult;
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
        CellConst::IS_LAND => false,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => true,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => true,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => false,
    ];

    protected string $shallowImagePath;
    protected string $seaImagePath;
    protected int $elevation = CellConst::ELEVATION_SHALLOW;

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
            $this->elevation = CellConst::ELEVATION_SHALLOW;
        }
    }

    public function getImagePath(): string
    {
        return $this->elevation === CellConst::ELEVATION_SHALLOW ? $this->shallowImagePath : $this->seaImagePath;
    }

    protected function move(Terrain $terrain, Cell $beforeCell, Cell $afterCell): Terrain
    {
        /** @var Ship $beforeCellCopy */
        $beforeCellCopy = deep_copy($beforeCell);
        $beforeCell->point = $afterCell->getPoint();
        $beforeCell->elevation = $afterCell->getElevation();
        $terrain->setCell($beforeCell->point, $beforeCell);

        $terrain->setCell($beforeCellCopy->getPoint(), CellConst::getDefaultCell($beforeCellCopy->getPoint(), $beforeCellCopy->getElevation()));

        return $terrain;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
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
