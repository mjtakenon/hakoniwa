<?php

namespace App\Entity\Log;

use App\Models\Island;

class IslandFoundLog extends LogRow
{
    private Island $island;
    private string $visibility;

    public function __construct(Island $island, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->visibility = $visibility;
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
