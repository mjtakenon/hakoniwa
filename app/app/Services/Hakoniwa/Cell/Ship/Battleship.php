<?php

namespace App\Services\Hakoniwa\Cell\Ship;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\AttackAndDefeatLog;
use App\Services\Hakoniwa\Log\AttackLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use function DeepCopy\deep_copy;

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
    protected int $offensivePower = 20;
    protected int $defencePower = 10;

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
        $logs = Logs::create();

        $combatantShips = $terrain->getTerrain()->flatten(1)->filter(function($cell) {
            return in_array($cell::TYPE, [Pirate::TYPE], true);
        });

        if ($combatantShips->count() >= 1) {
            // 艦船がいる場合、攻撃する
            /** @var CombatantShip $combatantShip */
            $combatantShip = $combatantShips->random();
            $moveTargetCells = $terrain->getAroundCells($combatantShip->getPoint())->filter(function ($cell) {
                return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
            });

            // 艦船の隣に移動できるセルがあった場合、移動する
            // なかった場合は、移動せず攻撃する
            if ($moveTargetCells->count() >= 1) {
                $terrain = $this->move($terrain, $this, $moveTargetCells->random());
            }

            $attackDamage = $this->getOffensiveDamage($combatantShip);
            $combatantShip->setDamage($combatantShip->getDamage() + $attackDamage);

            if ($combatantShip->damage >= 100) {
                $logs->add(new AttackAndDefeatLog($island, $turn, deep_copy($this), deep_copy($combatantShip), $attackDamage));
                if ($combatantShip->getElevation() === -1) {
                    $terrain->setCell($combatantShip->getPoint(), new Shallow(point: $combatantShip->getPoint()));
                } else {
                    $terrain->setCell($combatantShip->getPoint(), new Sea(point: $combatantShip->getPoint()));
                }
            } else {
                $logs->add(new AttackLog($island, $turn, deep_copy($this), deep_copy($combatantShip), $attackDamage));
                $terrain->setCell($combatantShip->getPoint(), $combatantShip);
            }
        }

        return parent::passTurn($island, $terrain, $status, $turn);
    }
}
