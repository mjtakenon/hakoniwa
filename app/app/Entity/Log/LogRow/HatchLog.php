<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Monster\Monster;
use App\Entity\Log\LogConst;
use App\Entity\Log\LogRow;
use App\Models\Island;

class HatchLog extends LogRow
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD ],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => LogConst::BOLD.LogConst::COLOR_WARNING ],
            ['text' => 'が孵り、中から'],
            ['text' => $this->monster::NAME, 'style' => LogConst::BOLD.LogConst::COLOR_DANGER ],
            ['text' => 'が出現！！' ],
        ]);
    }
}
