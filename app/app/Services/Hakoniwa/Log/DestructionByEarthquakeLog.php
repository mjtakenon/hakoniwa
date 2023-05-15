<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Cell;

class DestructionByEarthquakeLog extends LogRow
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
            ['text' => '地震により崩壊', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'しました。'],
        ]);
    }
}
