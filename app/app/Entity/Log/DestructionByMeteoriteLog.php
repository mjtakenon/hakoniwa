<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Models\Island;

class DestructionByMeteoriteLog extends LogRow
{
    private Island $island;
    private Cell $cell;

    public function __construct(Island $island, Cell $cell)
    {
        $this->island = $island;
        $this->cell = $cell;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'に'],
            ['text' => '隕石が落下', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            $this->cell::ELEVATION < 0 ? ['text' => 'し、海底がえぐられました。'] : ($this->cell::ELEVATION === 0 ? ['text' => 'し、一帯が水没しました。'] : ['text' => 'し、山が消し飛びました。']),
        ]);
    }
}
