<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Ship\Ship;

class DestructionByShipLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Cell $cell;
    private Ship $ship;

    public function __construct(Island $island, Turn $turn, Cell $cell, Ship $ship)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->cell = $cell;
        $this->ship = $ship;
    }

    public static function create(Island $island, Turn $turn, Cell $cell, Ship $ship)
    {
        return new static($island, $turn, $cell, $ship);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'は'],
            ['text' => $this->ship::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
            ['text' => 'に攻撃され、', 'style' => StyleConst::BOLD ],
            ['text' => '崩壊', 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
            ['text' => 'しました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
