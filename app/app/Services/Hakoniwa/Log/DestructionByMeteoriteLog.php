<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class DestructionByMeteoriteLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Cell $cell;

    public function __construct(Island $island, Turn $turn, Cell $cell, )
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->cell = $cell;
    }

    public static function create(Island $island, Turn $turn, Cell $cell)
    {
        return new static($island, $turn, $cell);
    }

    public function get(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'に'],
            ['text' => '隕石が落下' , 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER],
            $this->cell::ELEVATION < 0 ? ['text' => 'し、海底がえぐられました。'] : ($this->cell::ELEVATION === 0 ? ['text' => 'し、一帯が水没しました。'] : ['text' => 'し、山が消し飛びました。']),
        ]);
    }
}
