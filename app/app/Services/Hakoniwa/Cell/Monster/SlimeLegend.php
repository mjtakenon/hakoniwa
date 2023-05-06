<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByDividedMonsterLog;
use App\Services\Hakoniwa\Log\DestructionByMonsterLog;
use App\Services\Hakoniwa\Log\DisappearMonsterLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Rand;
use Illuminate\Support\Collection;

class SlimeLegend extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster19.gif';
    public const TYPE = 'slime_legend';
    public const NAME = '奇獣スライムレジェンド';
    public const DEFAULT_HIT_POINTS = 4;
    public const DEFAULT_MOVE_TIMES = 2;
    public const EXPERIENCE = 5;
    public const CORPSE_PRICE = 300;

    protected string $imagePath = self::IMAGE_PATH;
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
        $monsterCells = $terrain->getTerrain()->flatten(1)->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::IS_MONSTER];
        });

        if ($monsterCells->count() < 7) {
            return 1.0/3;
        }

        return 0.02;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn): PassTurnResult
    {
        if ($this->remainMoveTimes <= 0) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->getDisappearancePopulation() > $status->getPopulation()) {
            $logs = Logs::create();
            $logs->add(new DisappearMonsterLog($island, $turn, $this));
            $terrain->setCell($this->point, new Wasteland(point: $this->point));
            return new PassTurnResult($terrain, $status, $logs);
        }

        $this->remainMoveTimes -= 1;

        // 3回動く判定をし、すべて動けないセルだった場合は動かない
        $aroundCells = $terrain->getAroundCells($this->point);
        /** @var Collection $moveTargets */
        $moveTargets = $aroundCells->random(min(3, $aroundCells->count()))->filter(function ($cell) {
            return $cell::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_MONSTER];
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
            $terrain->setCell($this->point, new Wasteland(point: $this->point));

            $logs->add(new DestructionByMonsterLog($island, $turn, $moveTarget, $this));
            $monster->point = $moveTarget->point;
            // 移動先でさらに動く場合の操作をするため再帰呼び出しをしている
            $passTurnResult = $terrain->setCell($monster->getPoint(), $monster)->passTurn($island, $terrain, $status, $turn);

            $terrain = $passTurnResult->getTerrain();
            $status = $passTurnResult->getStatus();
            $logs->merge($passTurnResult->getLogs());

            return new PassTurnResult($terrain, $status, $logs);
        } else {
            $terrain->setCell($this->point, new SlimeLegend(point: $this->point, remain_move_times: 0, hit_points: 1));

            $logs->add(new DestructionByDividedMonsterLog($island, $turn, $moveTarget, $this));
            $monster->point = $moveTarget->point;
            // 移動先でさらに動く場合の操作をするため再帰呼び出しをしている
            $passTurnResult = $terrain->setCell($monster->getPoint(), $monster)->passTurn($island, $terrain, $status, $turn);

            $terrain = $passTurnResult->getTerrain();
            $status = $passTurnResult->getStatus();
            $logs->merge($passTurnResult->getLogs());

            return new PassTurnResult($terrain, $status, $logs);
        }
    }
}
