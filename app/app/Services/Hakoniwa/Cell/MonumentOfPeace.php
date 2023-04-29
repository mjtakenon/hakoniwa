<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class MonumentOfPeace extends Cell implements IPark
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument51.gif';
    public const TYPE = 'monument_of_peace';
    public const NAME = '平和の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 500;
    public const CONSTRUCTABLE_POPULATION_THRESHOLD = 500000;

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => true,
        CellTypeConst::PREVENTING_TYPHOON => true,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getImagePath(): string
    {
        return self::IMAGE_PATH;
    }

    public function toArray(bool $isPrivate = false): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString($isPrivate),
            ]
        ];
    }

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        if ($status->getPopulation() <= self::CONSTRUCTABLE_POPULATION_THRESHOLD) {
            return false;
        }
        if ($terrain->getTerrain()->flatten(1)->filter(function ($cell) { return $cell::TYPE === self::TYPE; })->count() >= 1 ) {
            return false;
        }
        return true;
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
    }
}
