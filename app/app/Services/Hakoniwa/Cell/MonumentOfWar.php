<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class MonumentOfWar extends Cell implements IPark
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument50.gif';
    public const TYPE = 'monument_of_war';
    public const NAME = '戦の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 500;
    public const CONSTRUCTABLE_BASE_LEVEL = 6;


    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_HUGE_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => true,
        CellTypeConst::PREVENTING_TYPHOON => true,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;
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

    public function getInfoString(bool $isPrivate = false): string
    {
        return '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME;
    }

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        if ($terrain->getTerrain()->flatten(1)->filter(function ($cell) { return $cell::TYPE === self::TYPE; })->count() >= 1 ) {
            return false;
        }

        $missileBaseCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) { return $cell::TYPE === MissileBase::TYPE; });
        /** @var MissileBase $missileBaseCell */
        foreach ($missileBaseCells as $missileBaseCell) {
            if ($missileBaseCell->getLevel() >= self::CONSTRUCTABLE_BASE_LEVEL) {
                return true;
            }
        }
        return false;
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
    }
}
