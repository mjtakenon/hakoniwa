<?php

namespace App\Services\Hakoniwa\Cell\Ship;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\AttackAndDefeatLog;
use App\Services\Hakoniwa\Log\AttackLog;
use App\Services\Hakoniwa\Log\DestructionByShipLog;
use App\Services\Hakoniwa\Log\DisappearPirateLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;
use function DeepCopy\deep_copy;

class Pirate extends CombatantShip
{
    public const SEA_IMAGE_PATH = '/img/hakoniwa/hakogif/pirate_sea.png';
    public const SHALLOW_IMAGE_PATH = '/img/hakoniwa/hakogif/pirate_shallow.png';
    public const TYPE = 'pirate';
    public const NAME = '海賊';
    public const AFFILIATION_PIRATE = -1;
    public const DEFAULT_RETURN_TURN = 5;
    protected ?int $affiliationId = self::AFFILIATION_PIRATE;

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
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

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandOccurEvents): PassTurnResult
    {
        $logs = Logs::create();

        // 他の島のもので規定ターンを過ぎていたら返す
        if (!is_null($this->getReturnTurn()) && $this->returnTurn <= $turn->turn) {
            $logs->add(new DisappearPirateLog($island, $turn, deep_copy($this)));
            if ($this->elevation === -1) {
                $terrain->setCell($this->getPoint(), new Shallow(point: $this->getPoint()));
            } else {
                $terrain->setCell($this->getPoint(), new Sea(point: $this->getPoint()));
            }
            return new PassTurnResult($terrain, $status, $logs);
        }

        $enemyShips = $terrain->getTerrain()->flatten(1)->filter(function($cell) {
            return in_array($cell::TYPE, [Battleship::TYPE, Submarine::TYPE], true);
        });

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
                $logs->add(new AttackAndDefeatLog($island, $turn, deep_copy($this), deep_copy($enemyShip), $attackDamage));
                if ($enemyShip->getElevation() === -1) {
                    $terrain->setCell($enemyShip->getPoint(), new Shallow(point: $enemyShip->getPoint()));
                } else {
                    $terrain->setCell($enemyShip->getPoint(), new Sea(point: $enemyShip->getPoint()));
                }
            } else {
                $logs->add(new AttackLog($island, $turn, deep_copy($this), deep_copy($enemyShip), $attackDamage));
                $terrain->setCell($enemyShip->getPoint(), $enemyShip);
            }
        } else {
            // 海岸沿いの建造物を破壊
            $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI] || $cell::ATTRIBUTE[CellTypeConst::IS_SHIP];
            });
            $seaCells = new Collection();

            /** @var Cell $candidate */
            foreach ($candidates as $candidate) {
                $seaCells = $seaCells->merge($terrain->getAroundCells($candidate->getPoint())->filter(function ($cell) {
                    return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
                }));
            }

            if ($seaCells->count() <= 0) {
                return parent::passTurn($island, $terrain, $status, $turn, $foreignIslandOccurEvents);
            }

            // 攻撃対象の隣へ移動
            /** @var Cell $seaCell */
            $seaCell = $seaCells->random();
            $terrain = $this->move($terrain, $this, $seaCell);

            // 攻撃対象のセルを取得し、攻撃
            $destroyTargetCells = $terrain->getAroundCells($seaCell->getPoint())->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI];
            });

            if ($destroyTargetCells->count() >= 1) {
                /** @var Cell $destroyTarget */
                $destroyTarget = $destroyTargetCells->random();
                $logs->add(new DestructionByShipLog($island, $turn, deep_copy($destroyTarget), deep_copy($this)));
                $terrain->setCell($destroyTarget->getPoint(), new Wasteland(point: $destroyTarget->getPoint()));
            }
        }

        return new PassTurnResult($terrain, $status, $logs);
    }
}
