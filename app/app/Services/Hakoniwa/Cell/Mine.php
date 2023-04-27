<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class Mine extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land15.gif';
    public const TYPE = 'mine';
    public const NAME = '採掘場';
    const PRODUCTION_NUMBER_OF_PEOPLE = 50000;
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_HUGE_METEORITE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];
    public const ELEVATION = 1;

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;

        if (array_key_exists('resourcesProductionNumberOfPeople', $data)) {
            $this->resourcesProductionNumberOfPeople = $data['resourcesProductionNumberOfPeople'];
        } else {
            $this->resourcesProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
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
                'resourcesProductionNumberOfPeople' => $this->resourcesProductionNumberOfPeople,
            ]
        ];
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            $this->resourcesProductionNumberOfPeople . '人規模';
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $this->resourcesProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_MINE_AND_OILFIELD_CAPACITY_AVAILABLE_POINTS) {
            $this->resourcesProductionNumberOfPeople *= 2;
        }
    }
}
