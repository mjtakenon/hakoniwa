<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;

class ReturnLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private string $visibility;
    private Cell $cell;
    private bool $isFrom;

    public function __construct(Island $island, Turn $turn, Cell $cell, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->visibility = $visibility;
        $this->cell = $cell;
        $this->isFrom = $isFrom;
    }

    public static function create(Island $island, Turn $turn, Cell $cell, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $cell, $isFrom, $visibility);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
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
