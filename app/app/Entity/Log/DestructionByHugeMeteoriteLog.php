<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Models\Island;

class DestructionByHugeMeteoriteLog extends LogRow
{
    private Island $island;
    private Cell $cell;
    private int $range;

    public function __construct(Island $island, Cell $cell, int $range)
    {
        $this->island = $island;
        $this->cell = $cell;
        $this->range = $range;
    }

    public function generate(): string
    {
        if ($this->range === 0 || $this->range === 1) {
            return json_encode([
                ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
                ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
                ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
                ['text' => 'は、'],
                $this->cell::ELEVATION < 0 ? ['text' => '海底がえぐられ'] : ['text' => '一帯が水没', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
                $this->cell::ELEVATION < 0 ? ['text' => 'ました。'] : ['text' => 'しました。'],
            ]);
        }

        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'は、衝撃波により'],
            ['text' => '一帯が壊滅', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'しました。'],
        ]);
    }
}
