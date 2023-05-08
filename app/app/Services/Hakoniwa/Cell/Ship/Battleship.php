<?php

namespace App\Services\Hakoniwa\Cell\Ship;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class Battleship extends CombatantShip
{
    public const MAINTENANCE_NUMBER_OF_PEOPLE = 5000;

    protected int $maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;

    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/battleship_sea.png';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/battleship_shallow.png';
    public const TYPE = 'battleship';
    public const NAME = '戦艦';

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['maintenanceNumberOfPeople'] = $this->maintenanceNumberOfPeople;
        return $arr;
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);
        $this->maintenanceNumberOfPeople = self::MAINTENANCE_NUMBER_OF_PEOPLE;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            '維持人数' . $this->maintenanceNumberOfPeople . '人' . PHP_EOL .
            $this->affiliationName . '島所属' . PHP_EOL .
            'レベル' . $this->getLevel() . ' 経験値:' . $this->experience .
            ($this->damage > 0 ? PHP_EOL . '破損率 ' . $this->damage . '%' : '');
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        // TODO: 海賊がいる場合の処理を追加
        return parent::passTurn($island, $terrain, $status, $turn);
    }
}
