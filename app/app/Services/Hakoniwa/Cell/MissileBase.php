<?php

namespace App\Services\Hakoniwa\Cell;

use App\Models\Island;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;

class MissileBase extends Cell implements IMissileFireable
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land9.gif';
    public const TYPE = 'missile_base';
    public const NAME = 'ミサイル基地';
    const MAINTENANCE_NUMBER_OF_PEOPLE = 20000;
    const SEASIDE_MAINTENANCE_NUMBER_OF_PEOPLE = 10000;

    private int $experience;

    private const EXPERIENCE_TABLE = [
        100 => 6,
        75 => 5,
        45 => 4,
        25 => 3,
        10 => 2,
        0 => 1,
    ];

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => true,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
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
                'image_path' => $isPrivate ? $this->imagePath : Forest::IMAGE_PATH,
                'info' => $this->getInfoString($isPrivate),
                'maintenanceNumberOfPeople' => $this->maintenanceNumberOfPeople,
                'experience' => $this->experience,
            ]
        ];
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('maintenanceNumberOfPeople', $data)) {
            $this->maintenanceNumberOfPeople = $data['maintenanceNumberOfPeople'];
        } else {
            $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;
        }

        if (array_key_exists('experience', $data)) {
            $this->experience = $data['experience'];
        } else {
            $this->experience = 0;
        }
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

    public function getInfoString(bool $isPrivate = false): string
    {
        if ($isPrivate) {
            return
                '('. $this->point->x . ',' . $this->point->y .') ' . $this->getName() . PHP_EOL .
                '維持人数' . $this->maintenanceNumberOfPeople . '人' . PHP_EOL .
                'レベル' . $this->getLevel() . ' 経験値:' . $this->experience;
        }
        return '('. $this->point->x . ',' . $this->point->y .') ' . Forest::NAME;
    }

    public function passTime(Island $island, Terrain $terrain, Status $status): void
    {
        $cells = $terrain->getAroundCells($this->point);
        $seasideCells = $cells->reject(function ($cell) { return $cell::ATTRIBUTE[CellTypeConst::IS_LAND]; });
        if ($seasideCells->count() >= 1) {
            $this->maintenanceNumberOfPeople = self::SEASIDE_MAINTENANCE_NUMBER_OF_PEOPLE;
        } else {
            $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;
        }
    }

    public function getLevel(): int
    {
        foreach(self::EXPERIENCE_TABLE as $exp => $level) {
            if ($this->experience >= $exp) {
                return $level;
            }
        }
        return 1;
    }

    public function setExperience(int $experience)
    {
        $this->experience = $experience;
    }

    public function getExperience(int $experience): int
    {
        return $experience;
    }
}
