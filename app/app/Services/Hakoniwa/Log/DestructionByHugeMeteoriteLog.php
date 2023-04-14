<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class DestructionByHugeMeteoriteLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Cell $cell;
    private int $range;

    public function __construct(Island $island, Turn $turn, Cell $cell, int $range)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->cell = $cell;
        $this->range = $range;
    }

    public static function create(Island $island, Turn $turn, Cell $cell, int $range)
    {
        return new static($island, $turn, $cell, $range);
    }

    public function get(): string
    {
        if ($this->range === 0 || $this->range === 1) {
            return json_encode([
                ['text' => 'ターン ' . $this->turn->turn . ' : '],
                ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
                ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
                ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
                ['text' => 'は、'],
                $this->cell::ELEVATION < 0 ? ['text' => '海底がえぐられ'] : ['text' => '一帯が水没', 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER],
                $this->cell::ELEVATION < 0 ? ['text' => 'ました。'] : ['text' => 'しました。'],
            ]);
        }

        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'は、衝撃波により'],
            ['text' => '一帯が壊滅', 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER],
            ['text' => 'しました。'],
        ]);
    }
}
