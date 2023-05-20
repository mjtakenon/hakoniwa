<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_WARNING],
            ['text' => 'に'],
            ['text' => '隕石が落下', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            $this->cell::ELEVATION < CellConst::ELEVATION_PLAIN ? ['text' => 'し、海底がえぐられました。'] : ($this->cell::ELEVATION === 0 ? ['text' => 'し、一帯が水没しました。'] : ['text' => 'し、山が消し飛びました。']),
        ]);
    }
}
