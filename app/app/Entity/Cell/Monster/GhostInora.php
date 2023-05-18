<?php

namespace App\Entity\Cell\Monster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\DestructionByMonsterLog;
use App\Entity\Log\DisappearMonsterLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

class GhostInora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster8.gif';
    public const TYPE = 'ghost_inora';
    public const NAME = '怪獣ゴーストいのら';
    public const DEFAULT_HIT_POINTS = 1;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 15;
    public const CORPSE_PRICE = 5000;

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

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if ($this->remainMoveTimes <= 0) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->getDisappearancePopulation() > $status->getPopulation()) {
            $logs = Logs::create();
            $logs->add(new DisappearMonsterLog($island, $this));
            $terrain->setCell($this->point, new Wasteland(point: $this->point));
            return new PassTurnResult($terrain, $status, $logs);
        }

        $this->remainMoveTimes -= 1;

        // 3回動く判定をし、すべて動けないセルだった場合は動かない
        $aroundCells = $terrain->getAroundCells($this->point, 4);
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
        $terrain->setCell($this->point, new Wasteland(point: $this->point));

        $logs->add(new DestructionByMonsterLog($island, $moveTarget, $this));
        $monster->point = $moveTarget->point;
        // 移動先でさらに動く場合の操作をするため再帰呼び出しをしている
        $terrain->setCell($monster->getPoint(), $monster);
$passTurnResult = $terrain->getCell($monster->getPoint())->passTurn($island, $terrain, $status, $turn, $foreignIslandEvents);

        $terrain = $passTurnResult->getTerrain();
        $status = $passTurnResult->getStatus();
        $logs->merge($passTurnResult->getLogs());

        return new PassTurnResult($terrain, $status, $logs);
    }
}
