<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;

class IslandFoundLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private string $visibility;

    public function __construct(Island $island, Turn $turn, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->visibility = $visibility;
    }

    public static function create(Island $island, Turn $turn, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $visibility);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'が発見されました！'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
