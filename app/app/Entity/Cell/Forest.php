<?php

namespace App\Entity\Cell;

use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class Forest extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land6.gif';
    public const TYPE = 'forest';
    public const NAME = '森';
    public const INITIAL_WOODS = 100;
    private const INCREMENT_WOODS = 100;
    private const MAX_WOODS = 100000;
    public const WOODS_TO_RESOURCES_COEF = 0.5;

    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::HAS_POPULATION => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellConst::PREVENTING_FIRE => true,
        CellConst::PREVENTING_TYPHOON => true,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('woods', $data)) {
            $this->woods = $data['woods'];
        } else {
            $this->woods = self::INITIAL_WOODS;
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['woods'] = $this->woods;
        return $arr;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        if ($isPrivate) {
            return
                '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
                $this->woods . '本';
        }
        return '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName();
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if ($this->woods >= self::MAX_WOODS) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }
        $this->woods += self::INCREMENT_WOODS;
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
