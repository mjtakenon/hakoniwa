<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Monster\Monster;
use App\Models\Island;

class AppearMonsterLog extends LogRow
{
    private Island $island;
    private Cell $cell;
    private Monster $monster;

    public function __construct(Island $island, Cell $cell, Monster $monster)
    {
        $this->island = $island;
        $this->cell = $cell;
        $this->monster = $monster;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') に'],
            ['text' => $this->monster::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => '出現！！ '],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'が'],
            ['text' => '踏み荒らされました。', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
        ]);
    }
}
