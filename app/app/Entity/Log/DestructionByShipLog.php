<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Entity\Cell\Ship\Ship;
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'は'],
            ['text' => $this->ship::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'に攻撃され、', 'style' => StyleConst::BOLD],
            ['text' => '崩壊', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'しました。'],
        ]);
    }
}
