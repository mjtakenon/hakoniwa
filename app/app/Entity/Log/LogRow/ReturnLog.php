<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class ReturnLog extends LogRow
{
    private Island $island;
    private Cell $cell;
    private bool $isFrom;

    public function __construct(Island $island, Cell $cell, bool $isFrom)
    {
        $this->island = $island;
        $this->cell = $cell;
        $this->isFrom = $isFrom;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            $this->isFrom ? ['text' => '所属の'] : ['text' => 'へ派遣していた'],
            ['text' => $this->cell->getName(), 'style' => LogConst::BOLD],
            $this->isFrom ? ['text' => 'は、任務を終え、帰っていきました。'] : ['text' => 'が帰還しました。']
        ]);
    }
}
