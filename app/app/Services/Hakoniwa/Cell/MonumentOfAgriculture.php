<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class MonumentOfAgriculture extends Cell implements IPark
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monument49.gif';
    public const TYPE = 'monument_of_agriculture';
    public const NAME = '農の碑';
    public const PRODUCT_DEVELOPMENT_POINTS = 250;
    public const CONSTRUCTABLE_FOODS_THRESHOLD = 120000;

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

    public static function canBuild(Terrain $terrain, Status $status): bool
    {
        if ($status->getProducedFoods() <= self::CONSTRUCTABLE_FOODS_THRESHOLD) {
            return false;
        }
        if ($terrain->getTerrain()->flatten(1)->filter(function ($cell) {
                return $cell::TYPE === self::TYPE;
            })->count() >= 1) {
            return false;
        }
        return true;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $status->setDevelopmentPoints($status->getDevelopmentPoints() + self::PRODUCT_DEVELOPMENT_POINTS);
        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
