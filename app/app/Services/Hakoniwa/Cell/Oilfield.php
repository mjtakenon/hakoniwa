<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\DevelopmentPointsConst;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class Oilfield extends Cell
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land16.gif';
    public const TYPE = 'oilfield';
    public const NAME = '油田';
    const PRODUCTION_NUMBER_OF_PEOPLE = 20000;
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
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => true,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => false,
    ];
    public const ELEVATION = -1;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

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

    public function getElevation(): int
    {
        return self::ELEVATION;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            $this->resourcesProductionNumberOfPeople . '人規模';
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $this->resourcesProductionNumberOfPeople = self::PRODUCTION_NUMBER_OF_PEOPLE;

        if ($status->getDevelopmentPoints() >= DevelopmentPointsConst::INCREMENT_MINE_AND_OILFIELD_CAPACITY_AVAILABLE_POINTS) {
            $this->resourcesProductionNumberOfPeople *= 2;
        }

        return new PassTurnResult($terrain, $status, Logs::create());
    }
}
