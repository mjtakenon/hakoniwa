<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Cell;

class DestructionByLandSubsidenceLog extends LogRow
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
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') の'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'は'],
            ['text' => '地盤が沈下', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            $this->cell::ELEVATION < 0 ? ['text' => 'しました'] : ['text' => 'し、一帯が水没しました。'],
        ]);
    }
}
