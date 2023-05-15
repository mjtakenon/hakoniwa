<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;

class AbortReturnLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private string $visibility;
    private Cell $cell;

    public function __construct(Island $island, Turn $turn, Cell $cell, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->visibility = $visibility;
        $this->cell = $cell;
    }

    public static function create(Island $island, Turn $turn, Cell $cell, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $cell, $visibility);
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
