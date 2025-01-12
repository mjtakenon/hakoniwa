<?php

namespace App\Entity\Cell\MissileFireable;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\HasWoods\Forest;
use App\Entity\Cell\IHasMaintenanceNumberOfPeople;
use App\Entity\Cell\MaintenanceInfo;
use App\Entity\Cell\Others\Sea;
use App\Models\Island;

class SeabedBase extends Cell implements IMissileFireable, IHasMaintenanceNumberOfPeople
{
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
        CellConst::IS_LAND => false,
        CellConst::IS_MONSTER => false,
        CellConst::IS_SHIP => false,
        CellConst::IS_MOUNTAIN => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => false,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => true,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => false,
    ];
    public const ELEVATION = CellConst::ELEVATION_SEA;

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
        return (new Sea(point: $this->point, elevation: $this->elevation))->toArray($isPrivate, $withStatic);
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

    public function getMaintenanceNumberOfPeople(Island $island): MaintenanceInfo
    {
        return new MaintenanceInfo($island->id, $this->maintenanceNumberOfPeople);
    }
}
