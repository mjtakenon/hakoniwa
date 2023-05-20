<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class ScatterAwayByVolcanicEruptionLog extends LogRow
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
            ['text' => 'は、'],
            ['text' => '火砕流にのみこまれ', 'style' => LogConst::BOLD],
            ['text' => 'ました。'],
        ]);
    }
}
