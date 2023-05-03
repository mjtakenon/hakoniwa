<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

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
        CellTypeConst::IS_LAND => true,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => true,
        CellTypeConst::PREVENTING_TYPHOON => true,
        CellTypeConst::PREVENTING_TSUNAMI => true,
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

    public function toArray(bool $isPrivate = false): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString($isPrivate),
                'woods' => $this->woods,
            ]
        ];
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

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        if ($this->woods >= self::MAX_WOODS) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }
        $this->woods += self::INCREMENT_WOODS;
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
