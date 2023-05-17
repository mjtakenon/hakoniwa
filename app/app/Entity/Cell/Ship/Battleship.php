<?php

namespace App\Entity\Cell\Ship;

use App\Entity\Cell\PassTurnResult;
use App\Entity\Cell\Sea;
use App\Entity\Cell\Shallow;
use App\Entity\Log\AttackAndDefeatLog;
use App\Entity\Log\AttackLog;
use App\Entity\Log\Logs;
use App\Entity\Plan\ForeignIsland\Event\ReturnShipToAffiliationIslandPlan;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
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

    public function getMaintenanceNumberOfPeople(Island $island): int
    {
        if ($island->id === $this->getAffiliationId()) {
            return parent::getMaintenanceNumberOfPeople($island);
        } else {
            return 0;
        }
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $logs = Logs::create();

        // TODO: 海賊以外が追加されたら増やす
        $enemyShips = $terrain->findByTypes([Pirate::TYPE]);

        if ($enemyShips->count() <= 0) {
            // ダメージを受けていて、戦闘していない場合は回復する
            if ($this->damage > 0) {
                $this->damage -= self::DEFAULT_HEAL_PER_TURN;
                $this->damage = max($this->damage, 0);
            }

            // 他の島のもので規定ターンを過ぎていたら返す
            if (!is_null($this->getReturnTurn()) && $this->returnTurn <= $turn->turn) {
                $foreignIslandOccurEvents->add(new ReturnShipToAffiliationIslandPlan($island->id, $this->getAffiliationId(), $this));
                return new PassTurnResult($terrain, $status, $logs);
            }
            return parent::passTurn($island, $terrain, $status, $turn, $foreignIslandOccurEvents);
        }

        // 艦船がいる場合、攻撃する
        /** @var CombatantShip $enemyShip */
        $enemyShip = $enemyShips->random();
        $moveTargetCells = $terrain->getAroundCells($enemyShip->getPoint())->filter(function ($cell) {
            return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
        });

        // 艦船の隣に移動できるセルがあった場合、移動する
        // なかった場合は、移動せず攻撃する
        if ($moveTargetCells->count() >= 1) {
            $terrain = $this->move($terrain, $this, $moveTargetCells->random());
        }

        $attackDamage = $this->getOffensiveDamage($enemyShip);
        $enemyShip->setDamage($enemyShip->getDamage() + $attackDamage);

        if ($enemyShip->damage >= 100) {
            $attackDamage -= ($enemyShip->damage - 100);
            $enemyShip->damage = 100;

            $logs->add(new AttackAndDefeatLog($island, deep_copy($this), deep_copy($enemyShip), $attackDamage));
            if ($enemyShip->getElevation() === -1) {
                $terrain->setCell($enemyShip->getPoint(), new Shallow(point: $enemyShip->getPoint()));
            } else {
                $terrain->setCell($enemyShip->getPoint(), new Sea(point: $enemyShip->getPoint()));
            }

            // TODO: 得られる経験値は変数に切り出す
            $this->experience += $enemyShip->getLevel() * 5;
        } else {
            $logs->add(new AttackLog($island, deep_copy($this), deep_copy($enemyShip), $attackDamage));
            $terrain->setCell($enemyShip->getPoint(), $enemyShip);
        }

        return new PassTurnResult($terrain, $status, $logs);
    }
}