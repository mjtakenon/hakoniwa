<?php

namespace App\Entity\Cell\Ship;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Sea;
use App\Entity\Cell\Others\Shallow;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\LogRow\AttackAndDefeatLog;
use App\Entity\Log\LogRow\AttackLog;
use App\Entity\Log\LogRow\DestructionByShipLog;
use App\Entity\Log\LogRow\DisappearEnemyShipLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class Pirate extends CombatantShip
{
    public const TYPE = 'pirate';
    public const NAME = '海賊';
    public const AFFILIATION_ENEMY = -1;
    public const DEFAULT_RETURN_TURN = 5;
    protected ?int $affiliationId = self::AFFILIATION_ENEMY;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $offensivePower = 20;
    protected int $defencePower = 10;

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            'レベル' . $this->getLevel() . ' 経験値:' . $this->experience .
            ($this->damage > 0 ? PHP_EOL . '破損率 ' . $this->damage . '%' : '');
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        $logs = Logs::create();

        // 他の島のもので規定ターンを過ぎていたら返す
        if (!is_null($this->getReturnTurn()) && $this->returnTurn <= $turn->turn) {
            $logs->add(new DisappearEnemyShipLog($island, deep_copy($this)));
            $terrain->setCell($this->getPoint(), CellConst::getDefaultCell($this->getPoint(), $this->getElevation()));
            return new PassTurnResult($terrain, $status, $logs);
        }

        $enemyShips = $terrain->findByTypes([Battleship::TYPE, Submarine::TYPE]);

        if ($enemyShips->count() >= 1) {
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
                $attackDamage -= $enemyShip->damage - 100;
                $enemyShip->damage = 100;
                $logs->add(new AttackAndDefeatLog($island, deep_copy($this), deep_copy($enemyShip), $attackDamage));
                $terrain->setCell($enemyShip->getPoint(), CellConst::getDefaultCell($enemyShip->getPoint(), $enemyShip->getElevation()));
            } else {
                $logs->add(new AttackLog($island, deep_copy($this), deep_copy($enemyShip), $attackDamage));
                $terrain->setCell($enemyShip->getPoint(), $enemyShip);
            }
        } else {
            // 海岸沿いの建造物を破壊
            $candidates = $terrain->getCells()->flatten(1)->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellConst::DESTRUCTIBLE_BY_TSUNAMI] || $cell::ATTRIBUTE[CellConst::IS_SHIP];
            });
            $seaCells = new Collection();

            /** @var Cell $candidate */
            foreach ($candidates as $candidate) {
                $seaCells = $seaCells->merge($terrain->getAroundCells($candidate->getPoint())->filter(function ($cell) {
                    return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
                }));
            }

            if ($seaCells->count() <= 0) {
                return parent::passTurn($island, $terrain, $status, $turn, $foreignIslandEvents);
            }

            // 攻撃対象の隣へ移動
            /** @var Cell $seaCell */
            $seaCell = $seaCells->random();
            $terrain = $this->move($terrain, $this, $seaCell);

            // 攻撃対象のセルを取得し、攻撃
            $destroyTargetCells = $terrain->getAroundCells($seaCell->getPoint())->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellConst::DESTRUCTIBLE_BY_TSUNAMI];
            });

            if ($destroyTargetCells->count() >= 1) {
                /** @var Cell $destroyTarget */
                $destroyTarget = $destroyTargetCells->random();
                $logs->add(new DestructionByShipLog($island, deep_copy($destroyTarget), deep_copy($this)));
                $terrain->setCell($destroyTarget->getPoint(), new Wasteland(point: $destroyTarget->getPoint()));
            }
        }

        return new PassTurnResult($terrain, $status, $logs);
    }
}
