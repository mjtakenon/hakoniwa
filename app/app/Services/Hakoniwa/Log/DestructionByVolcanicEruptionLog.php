<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class DestructionByVolcanicEruptionLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Cell $cell;

    public function __construct(Island $island, Turn $turn, Cell $cell)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->cell = $cell;
    }

    public static function create(Island $island, Turn $turn, Cell $cell)
    {
        return new static($island, $turn, $cell);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'は'],
            $this->cell::ELEVATION < 0 ? ['text' => '海底が隆起'] : ['text' => '火砕流', 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER],
            $this->cell::ELEVATION < 0 ? ['text' => 'しました。'] : ['text' => 'にのみこまれました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
