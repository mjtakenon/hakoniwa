<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class OccurMeteoriteLog extends LogRow
{
    private Island $island;

    public function __construct(Island $island)
    {
        $this->island = $island;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => 'に'],
            ['text' => '隕石群', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'が襲来！'],
        ]);
    }
}
