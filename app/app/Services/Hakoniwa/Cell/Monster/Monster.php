<?php

namespace App\Services\Hakoniwa\Cell\Monster;

use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\CellTypeConst;

abstract class Monster extends Cell
{
    const ATTRIBUTE = [
        CellTypeConst::IS_LAND => true,
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
}
