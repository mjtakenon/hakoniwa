<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class Forest extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land6.gif';
    public const TYPE = 'forest';
    public const NAME = '森';
    public const INITIAL_WOODS = 100;
    private const INCREMENT_WOODS = 100;
    private const MAX_WOODS = 100000;

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_HUGE_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => true,
        CellTypeConst::PREVENTING_TYPHOON => true,
        CellTypeConst::PREVENTING_TSUNAMI => true,

    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;

        if (array_key_exists('woods', $data)) {
            $this->woods = $data['woods'];
        } else {
            $this->woods = self::INITIAL_WOODS;
        }
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
                'woods' => $this->woods,
            ]
        ];
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            $this->woods . '本';
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        if ($this->woods >= self::MAX_WOODS) {
            return;
        }
        $this->woods += self::INCREMENT_WOODS;
    }
}
