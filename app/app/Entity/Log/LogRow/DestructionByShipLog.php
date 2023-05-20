<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Ship\Ship;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class DestructionByShipLog extends LogRow
{
    private Island $island;
    private Cell $cell;
    private Ship $ship;

    public function __construct(Island $island, Cell $cell, Ship $ship)
    {
        $this->island = $island;
        $this->cell = $cell;
        $this->ship = $ship;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_WARNING],
            ['text' => 'は'],
            ['text' => $this->ship::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'に攻撃され、', 'style' => LogConst::BOLD],
            ['text' => '崩壊', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'しました。'],
        ]);
    }
}
