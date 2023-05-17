<?php

namespace App\Entity\Cell\MissileBase;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellTypeConst;
use App\Entity\Cell\Forest;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

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
        CellTypeConst::IS_MONSTER => false,
        CellTypeConst::IS_SHIP => false,
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

    protected string $imagePath = self::IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        if ($isPrivate) {
            $arr = parent::toArray($isPrivate, $withStatic);
            $arr['data']['maintenanceNumberOfPeople'] = $this->maintenanceNumberOfPeople;
            $arr['data']['experience'] = $this->experience;
            return $arr;
        }
        return (new Forest(point: $this->point))->toArray($isPrivate, $withStatic);
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

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $cells = $terrain->getAroundCells($this->point);
        $seasideCells = $cells->reject(function ($cell) { return $cell::ATTRIBUTE[CellTypeConst::IS_LAND]; });
        if ($seasideCells->count() >= 1) {
            $this->maintenanceNumberOfPeople = self::SEASIDE_MAINTENANCE_NUMBER_OF_PEOPLE;
        } else {
            $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;
        }
        return new PassTurnResult($terrain, $status, Logs::create());
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

    public function getExperience(): int
    {
        return $this->experience;
    }
}
