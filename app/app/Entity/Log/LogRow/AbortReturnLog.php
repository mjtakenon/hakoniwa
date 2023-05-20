<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class AbortReturnLog extends LogRow
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
            ['text' => '所属の'],
            ['text' => $this->cell->getName(), 'style' => LogConst::BOLD],
            ['text' => 'は、所属島に空きがないため帰還できませんでした。'],
        ]);
    }
}
