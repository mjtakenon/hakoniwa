<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByMonsterLog;
use App\Services\Hakoniwa\Log\DisappearMonsterLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;
use Illuminate\Support\Collection;

class Hamunemu extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster16.gif';
    public const TYPE = 'hamunemu';
    public const NAME = '怪獣はむねむ';
    public const DEFAULT_HIT_POINTS = 2;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 8;
    public const CORPSE_PRICE = 3000;

    public function getName(): string
    {
        return self::NAME;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getImagePath(): string
    {
        return self::IMAGE_PATH;
    }

    public function getAppearancePopulation(): int
    {
        return MonsterConst::APPEARANCE_POPULATION_LV2;
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
        $aroundCells = $terrain->getAroundCells($this->point, 2);
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
        $terrain->setCell($this->point, new Wasteland(point: $this->point));

        $logs->add(new DestructionByMonsterLog($island, $turn, $moveTarget, $this));
        $monster->point = $moveTarget->point;
        // 移動先でさらに動く場合の操作をするため再帰呼び出しをしている
        $passTurnResult = $terrain->setCell($monster->getPoint(), $monster)->passTurn($island, $terrain, $status, $turn);

        $terrain = $passTurnResult->getTerrain();
        $status = $passTurnResult->getStatus();
        $logs->merge($passTurnResult->getLogs());

        return new PassTurnResult($terrain, $status, $logs);
    }
}