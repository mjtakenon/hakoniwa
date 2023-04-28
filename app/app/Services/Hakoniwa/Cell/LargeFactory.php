<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class LargeFactory extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land86.gif';
    public const TYPE = 'large_factory';
    public const NAME = '大工場';
    const PRODUCTION_NUMBER_OF_PEOPLE = 40000;
    const SEASIDE_PRODUCTION_NUMBER_OF_PEOPLE = 60000;

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => true,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => true,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => true,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
    ];

    public function toArray(bool $isPrivate = false): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString($isPrivate),
                'fundsProductionNumberOfPeople' => $this->fundsProductionNumberOfPeople,
            ]
        ];
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->imagePath = self::IMAGE_PATH;
        $this->type = self::TYPE;

        if (array_key_exists('fundsProductionNumberOfPeople', $data)) {
            $this->fundsProductionNumberOfPeople = $data['fundsProductionNumberOfPeople'];
        } else {
            $this->fundsProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
        }
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '('. $this->point->x . ',' . $this->point->y .') ' . self::NAME . PHP_EOL .
            $this->fundsProductionNumberOfPeople . '人規模';
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $cells = $terrain->getAroundCells($this->point);
        $seasideCells = $cells->reject(function ($cell) { return $cell::ATTRIBUTE[CellTypeConst::IS_LAND]; });
        if ($seasideCells->count() >= 1) {
            $this->fundsProductionNumberOfPeople = self::SEASIDE_PRODUCTION_NUMBER_OF_PEOPLE;
        } else {
            $this->fundsProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;
        }
    }
}
