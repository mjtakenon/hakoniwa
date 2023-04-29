<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class AppeardMonsterLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Cell $cell;
    private Monster $monster;

    public function __construct(Island $island, Turn $turn, Cell $cell, Monster $monster)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->cell = $cell;
        $this->monster = $monster;
    }

    public static function create(Island $island, Turn $turn, Cell $cell, Monster $monster)
    {
        return new static($island, $turn, $cell, $monster);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') に'],
            ['text' => $this->monster::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
            ['text' => '出現！！ '],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'が'],
            ['text' => '踏み荒らされました。', 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
