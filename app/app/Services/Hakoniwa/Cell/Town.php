<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

class Town extends Cell
{
    const IMAGE_PATH = '/img/hakoniwa/hakogif/land4.gif';
    const TYPE = 'town';
    const NAME = '町';
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => true,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => true,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => true,
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
        $this->population = $data['population'];
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
                'population' => $this->population,
            ]
        ];
    }

    public function getInfoString(): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            '人口 ' . $this->population . '人';
    }
}
