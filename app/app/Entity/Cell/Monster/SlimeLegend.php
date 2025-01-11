<?php

namespace App\Entity\Cell\Monster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\LogRow\DestructionByDividedMonsterLog;
use App\Entity\Log\LogRow\DestructionByMonsterLog;
use App\Entity\Log\LogRow\DisappearMonsterLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Entity\Util\Rand;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class SlimeLegend extends Monster
{
    public const TYPE = 'slime_legend';
    public const NAME = '奇獣スライムレジェンド';
    public const DEFAULT_HIT_POINTS = 4;
    public const DEFAULT_MOVE_TIMES = 2;
    public const EXPERIENCE = 5;
    public const CORPSE_PRICE = 300;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;


    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV3;
    }

    public function getCorpsePrice(): int
    {
        return self::CORPSE_PRICE;
    }

    public function getExperience(): int
    {
        return self::EXPERIENCE;
    }

    public function getDefaultHitPoints(): int
    {
        return self::DEFAULT_HIT_POINTS;
    }

    public function getDefaultMoveTimes(): int
    {
        return self::DEFAULT_MOVE_TIMES;
    }

    private function getDivisionProbably(Terrain $terrain): float
    {
        $monsterCells = $terrain->findByAttribute(CellConst::IS_MONSTER);

        if ($monsterCells->count() < 7) {
            return 1.0/3;
        }

        return 0.02;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if ($this->remainMoveTimes <= 0) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->getDisappearancePopulation() > $status->getPopulation()) {
            $logs = Logs::create();
            $logs->add(new DisappearMonsterLog($island, $this));
            $terrain->setCell(new Wasteland(point: $this->point));
            return new PassTurnResult($terrain, $status, $logs);
        }

        $this->remainMoveTimes -= 1;

        // 3回動く判定をし、すべて動けないセルだった場合は動かない
        $aroundCells = $terrain->getAroundCells($this->point);
        /** @var Collection $moveTargets */
        $moveTargets = $aroundCells->random(min(3, $aroundCells->count()))->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellConst::DESTRUCTIBLE_BY_MONSTER];
        });

        if ($moveTargets->count() <= 0) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        /** @var Cell $moveTarget */
        $moveTarget = $moveTargets->random();
        $logs = Logs::create();
        // 破壊する際、消えないよう元のデータを持っておく
        $monster = $this;

        // 1/3の確率で分裂, 分裂確率は島のモンスター数により変化
        // 3/2の確率で移動
        if ($this->getDivisionProbably($terrain) <= Rand::mt_rand_float()) {
            $terrain->setCell(new Wasteland(point: $this->point));

            $logs->add(new DestructionByMonsterLog($island, $moveTarget, $this));
            $monster->point = $moveTarget->point;
            // 移動先でさらに動く場合の操作をするため再帰呼び出しをしている
            $terrain->setCell($monster);
$passTurnResult = $terrain->getCell($monster->getPoint())->passTurn($island, $terrain, $status, $turn, $foreignIslandEvents);

            $terrain = $passTurnResult->getTerrain();
            $status = $passTurnResult->getStatus();
            $logs->merge($passTurnResult->getLogs());

            return new PassTurnResult($terrain, $status, $logs);
        } else {
            $terrain->setCell(new SlimeLegend(point: $this->point, remain_move_times: 0, hit_points: 1));

            $logs->add(new DestructionByDividedMonsterLog($island, $moveTarget, $this));
            $monster->point = $moveTarget->point;
            // 移動先でさらに動く場合の操作をするため再帰呼び出しをしている
            $terrain->setCell($monster);
$passTurnResult = $terrain->getCell($monster->getPoint())->passTurn($island, $terrain, $status, $turn, $foreignIslandEvents);

            $terrain = $passTurnResult->getTerrain();
            $status = $passTurnResult->getStatus();
            $logs->merge($passTurnResult->getLogs());

            return new PassTurnResult($terrain, $status, $logs);
        }
    }
}
