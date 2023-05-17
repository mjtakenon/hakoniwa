<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Models\Island;

class AbortReturnLog extends LogRow
{
    private Island $island;
    private string $visibility;
    private Cell $cell;

    public function __construct(Island $island, Cell $cell, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->visibility = $visibility;
        $this->cell = $cell;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => '所属の'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD],
            ['text' => '所属の'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
