<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;
use App\Services\Hakoniwa\Cell\PassTurnResult;
use App\Services\Hakoniwa\Cell\Wasteland;
use App\Services\Hakoniwa\Log\DestructionByMonsterLog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Plan\ExecutePlanResult;
use App\Services\Hakoniwa\Status\Status;
use App\Services\Hakoniwa\Terrain\Terrain;

class Inora extends Monster
{
    public const IMAGE_PATH = '/img/hakoniwa/hakogif/monster0.gif';
    public const TYPE = 'inora';
    public const NAME = '怪獣いのら';
    public const DEFAULT_HIT_POINTS = 1;
    public const DEFAULT_MOVE_TIMES = 1;
    public const EXPERIENCE = 5;
    public const CORPSE_PRICE = 1500;

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
        return MonsterConst::APPEARANCE_POPULATION_LV1;
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
        $this->remainMoveTimes -= 1;

        $aroundCells = $terrain->getAroundCells($this->point);
        /** @var Cell $moveTarget */
        $moveTarget = $aroundCells->random();
        if (!$moveTarget::ATTRIBUTE[CellTypeConst::DESTRUCTIBLE_BY_MONSTER]) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        $logs = Logs::create();
        $monster = $this;
        $terrain->setCell($this->point, new Wasteland(point: $this->point));

        $logs->add(new DestructionByMonsterLog($island, $turn, $moveTarget, $this));
        $monster->point = $moveTarget->point;
        $passTurnResult = $terrain->setCell($monster->getPoint(), $monster)->passTurn($island, $terrain, $status, $turn);

        $terrain = $passTurnResult->getTerrain();
        $status = $passTurnResult->getStatus();
        $logs->merge($passTurnResult->getLogs());

        return new PassTurnResult($terrain, $status, $logs);
    }
}
