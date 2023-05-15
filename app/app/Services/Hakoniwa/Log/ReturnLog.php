<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Cell;

class ReturnLog extends LogRow
{
    private Island $island;
    private string $visibility;
    private Cell $cell;
    private bool $isFrom;

    public function __construct(Island $island, Cell $cell, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->visibility = $visibility;
        $this->cell = $cell;
        $this->isFrom = $isFrom;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            $this->isFrom ? ['text' => '所属の'] : ['text' => 'へ派遣していた'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD],
            $this->isFrom ? ['text' => 'は、任務を終え、帰っていきました。'] : ['text' => 'が帰還しました。']
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
