<?php

namespace App\Entity\Cell\MissileBase;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellTypeConst;
use App\Entity\Cell\Forest;
use App\Entity\Cell\Sea;

class SeabedBase extends Cell implements IMissileFireable
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/land12.gif';
    public const TYPE = 'seabed_base';
    public const NAME = '海底基地';
    const MAINTENANCE_NUMBER_OF_PEOPLE = 20000;

    private int $experience;

    private const EXPERIENCE_TABLE = [
        100 => 3,
        40 => 2,
        0 => 1,
    ];

    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => false,
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::IS_SHIP => false,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => false,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => true,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => false,
    ];
    public const ELEVATION = -2;

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $elevation = self::ELEVATION;

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;

        if (array_key_exists('experience', $data)) {
            $this->experience = $data['experience'];
        } else {
            $this->experience = 0;
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        if ($isPrivate) {
            $arr = parent::toArray($isPrivate, $withStatic);
            $arr['data']['maintenanceNumberOfPeople'] = $this->maintenanceNumberOfPeople;
            $arr['data']['experience'] = $this->experience;
            return $arr;
        }
        return (new Sea(point: $this->point))->toArray($isPrivate, $withStatic);
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        if ($isPrivate) {
            return
                '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
                '維持人数' . $this->maintenanceNumberOfPeople . '人' . PHP_EOL .
                'レベル' . $this->getLevel() . ' 経験値:' . $this->experience;
        }
        return '(' . $this->point->x . ',' . $this->point->y . ') ' . Forest::NAME;
    }

    public function getLevel(): int
    {
        foreach (self::EXPERIENCE_TABLE as $exp => $level) {
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

    public function getExperience(): int
    {
        return $this->experience;
    }
}
