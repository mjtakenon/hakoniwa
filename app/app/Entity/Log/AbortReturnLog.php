<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => '所属の'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、所属島に空きがないため帰還できませんでした。'],
        ]);
    }
}
