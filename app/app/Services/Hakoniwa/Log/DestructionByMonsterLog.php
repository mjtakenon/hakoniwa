<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Monster\Monster;

class DestructionByMonsterLog extends LogRow
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
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'が'],
            ['text' => $this->monster::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'に踏み荒らされました。', 'style' => StyleConst::BOLD],
        ]);
    }
}
