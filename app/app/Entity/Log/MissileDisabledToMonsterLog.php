<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Entity\Plan\Plan;
use App\Models\Island;

class MissileDisabledToMonsterLog extends LogRow
{
    private Island $island;
    private Cell $cell;
    private Plan $plan;

    public function __construct(Island $island, Cell $cell, Plan $plan)
    {
        $this->island = $island;
        $this->cell = $cell;
        $this->plan = $plan;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'から発射された'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD],
            ['text' => 'は'],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') ', 'style' => StyleConst::BOLD],
            ['text' => 'の'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'に命中しましたが、'],
            ['text' => '無効化', 'style' => StyleConst::BOLD],
            ['text' => 'されました。'],
        ]);
    }
}
