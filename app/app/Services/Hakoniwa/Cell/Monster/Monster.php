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

abstract class Monster extends Cell
{
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
        CellTypeConst::IS_MONSTER => true,
        CellTypeConst::HAS_POPULATION => false,
        CellTypeConst::DESTRUCTIBLE_BY_FIRE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TSUNAMI => false,
        CellTypeConst::DESTRUCTIBLE_BY_EARTHQUAKE => false,
        CellTypeConst::DESTRUCTIBLE_BY_TYPHOON => false,
        CellTypeConst::DESTRUCTIBLE_BY_METEORITE => true,
        CellTypeConst::DESTRUCTIBLE_BY_WIDE_AREA_DAMAGE_2HEX => true,
        CellTypeConst::DESTRUCTIBLE_BY_MISSILE => false,
        CellTypeConst::DESTRUCTIBLE_BY_RIOT => false,
        CellTypeConst::DESTRUCTIBLE_BY_MONSTER => false,
        CellTypeConst::PREVENTING_FIRE => false,
        CellTypeConst::PREVENTING_TYPHOON => false,
        CellTypeConst::PREVENTING_TSUNAMI => true,
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

    public function toArray(bool $isPrivate = false): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString($isPrivate),
                'hit_points' => $this->getHitPoints(),
            ]
        ];
    }

    public function getInfoString(bool $isPrivate = false): string
    {
        return
            '(' . $this->point->x . ',' . $this->point->y . ') ' . $this->getName() . PHP_EOL .
            '体力 ' . $this->getHitPoints();
    }

    abstract public function getAppearancePopulation(): int;

    public function getDisappearancePopulation(): int
    {
        return $this->getAppearancePopulation() / 4;
    }

    abstract public function getExperience(): int;

    abstract public function getCorpsePrice(): int;

    public function getHitPoints(): int
    {
        return $this->hitPoints;
    }

    public function setHitPoints($hitPoints): void
    {
        $this->hitPoints = $hitPoints;
    }

    abstract public function getDefaultHitPoints(): int;

    abstract public function getDefaultMoveTimes(): int;

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
