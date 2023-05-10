<?php

namespace App\Services\Hakoniwa\Cell\Ship;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Sea;
use App\Services\Hakoniwa\Cell\Shallow;
use App\Services\Hakoniwa\Log\AttackAndDefeatLog;
use App\Services\Hakoniwa\Log\AttackLog;
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
    protected ?int $affiliationId = self::AFFILIATION_PIRATE;

    protected string $shallowImagePath = self::SHALLOW_IMAGE_PATH;
    protected string $seaImagePath = self::SEA_IMAGE_PATH;
    protected string $type = self::TYPE;
    protected string $name = self::NAME;

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        return $arr;
    }

    public function __construct(...$data)
    {
        parent::__construct(...$data);
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            'レベル' . $this->getLevel() . ' 経験値:' . $this->experience .
            ($this->damage > 0 ? PHP_EOL . '破損率 ' . $this->damage . '%' : '');
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        $logs = Logs::create();

        $combatantShips = $terrain->getTerrain()->flatten(1)->filter(function($cell) {
            return in_array($cell::TYPE, [Battleship::TYPE, Submarine::TYPE], true);
        });

        if ($combatantShips->count() >= 1) {
            // 艦船がいる場合、攻撃する
            /** @var CombatantShip $combatantShip */
            $combatantShip = $combatantShips->random();
            $moveTargetCells = $terrain->getAroundCells($combatantShip->getPoint())->filter(function ($cell) {
                return in_array($cell::TYPE, [Sea::TYPE, Shallow::TYPE], true);
            });

            // 艦船の隣に移動できるセルがあった場合、移動する
            // なかった場合は、移動せず攻撃もしない
            if ($moveTargetCells->count() >= 1) {
                /** @var Cell $moveTargetCell */
                $moveTargetCell = $moveTargetCells->random();
                $this->point = $moveTargetCell->getPoint();
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
        } else {
            // 海岸沿いの建造物を壊す

            $candidates = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
                return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI];
            });

            $aroundCells = new Collection();

            /** @var Cell $cell */
            foreach ($candidates as $cell) {
                $aroundCells->push($terrain->getAroundCells($cell->getPoint(), 1, true));
            }

            if ($aroundCells->count() <= 0) {
                return parent::passTurn($island, $terrain, $status, $turn);
            }


        }

        return parent::passTurn($island, $terrain, $status, $turn);
    }
}
