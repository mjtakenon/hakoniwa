<?php

namespace App\Entity\Cell\Monster;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Cell\PassTurnResult;
use App\Entity\Log\LogRow\DestructionByMonsterLog;
use App\Entity\Log\LogRow\DisappearMonsterLog;
use App\Entity\Log\Logs;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Support\Collection;

abstract class Monster extends Cell implements IMonster
{
    const ATTRIBUTE = [
        CellConst::IS_LAND => true,
        CellConst::IS_MONSTER => true,
        CellConst::IS_SHIP => false,
        CellConst::IS_MOUNTAIN => false,
        CellConst::DESTRUCTIBLE_BY_FIRE => false,
        CellConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellConst::DESTRUCTIBLE_BY_RIOT => false,
        CellConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellConst::PREVENTING_FIRE => false,
        CellConst::PREVENTING_TYPHOON => false,
        CellConst::PREVENTING_TSUNAMI => true,
    ];

    public int $hitPoints;
    public int $remainMoveTimes = 1;

    public function __construct(...$data)
    {
        parent::__construct(...$data);

        if (array_key_exists('hit_points', $data)) {
            $this->hitPoints = $data['hit_points'];
        } else {
            $this->hitPoints = $this->getDefaultHitPoints();
        }

        if (array_key_exists('remain_move_times', $data)) {
            $this->remainMoveTimes = $data['remain_move_times'];
        } else {
            $this->remainMoveTimes = $this->getDefaultMoveTimes();
        }
    }

    public function toArray(bool $isPrivate = false, bool $withStatic = false): array
    {
        $arr = parent::toArray($isPrivate, $withStatic);
        $arr['data']['hit_points'] = $this->hitPoints;
        return $arr;
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            '体力 ' . $this->getHitPoints();
    }

    public function getDisappearancePopulation(): int
    {
        return $this->getAppearancePopulation() / 4;
    }

    public function getHitPoints(): int
    {
        return $this->hitPoints;
    }

    public function setHitPoints($hitPoints): void
    {
        $this->hitPoints = $hitPoints;
    }

    public function isAttackDisabled(): bool
    {
        return false;
    }

    public function passTurn(Island $island, Terrain $terrain, Status $status, Turn $turn, Collection $foreignIslandEvents): PassTurnResult
    {
        if ($this->remainMoveTimes <= 0) {
            return new PassTurnResult($terrain, $status, Logs::create());
        }

        if ($this->getDisappearancePopulation() > $status->getPopulation()) {
            $logs = Logs::create();
            $logs->add(new DisappearMonsterLog($island, $this));
            $terrain->setCell(new Wasteland(point: $this->point, elevation: $this->elevation));
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
        $terrain->setCell(new Wasteland(point: $this->point, elevation: $this->elevation));

        $logs->add(new DestructionByMonsterLog($island, $moveTarget, $this));
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
