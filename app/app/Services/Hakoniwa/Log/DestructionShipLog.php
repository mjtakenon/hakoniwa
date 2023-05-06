<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;

class DestructionShipLog implements ILog
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

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'は'],
            ['text' => '海の藻屑になりました。' , 'style' => StyleConst::BOLD],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
