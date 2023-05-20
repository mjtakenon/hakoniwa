<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class IslandFoundLog extends LogRow
{
    private Island $island;

    public function __construct(Island $island)
    {
        $this->island = $island;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD ],
            ['text' => 'が発見されました！'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogConst::VISIBILITY_GLOBAL;
    }
}
