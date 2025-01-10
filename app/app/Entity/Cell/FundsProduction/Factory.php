<?php

namespace App\Entity\Cell\FundsProduction;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Factory extends Cell implements IFundsProduction
{
    public const TYPE = 'factory';
    public const NAME = '工場';
    const PRODUCTION_CAPACITY = 20000;
    const SEASIDE_PRODUCTION_CAPACITY = 30000;

    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::IS_MOUNTAIN => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => true,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => true,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellConst::DESTRUCTIBLE_BY_RIOT => true,
        CellConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $productionCapacity = self::PRODUCTION_CAPACITY;
    protected int $seasideProductionCapacity = self::SEASIDE_PRODUCTION_CAPACITY;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['fundsProductionCapacity'] = $this->fundsProductionCapacity;
        return $arr;
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('fundsProductionCapacity', $data)) {
            $this->fundsProductionCapacity = $data['fundsProductionCapacity'];
        } else {
            $this->fundsProductionCapacity = $this->productionCapacity;
        }
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            $this->fundsProductionCapacity . '人規模';
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $cells = $terrain->getAroundCells($this->point);
        $seasideCells = $cells->reject(function ($cell) {
            return $cell::ATTRIBUTE[CellConst::IS_LAND];
        });
        if ($seasideCells->count() >= 1) {
            $this->fundsProductionCapacity = $this->seasideProductionCapacity;
        } else {
            $this->fundsProductionCapacity = $this->productionCapacity;
        }
        return new PassTurnResult($terrain, $status, Logs::create());
    }

    public function getFundsProductionCapacity(): int
    {
        return $this->fundsProductionCapacity;
    }
}
