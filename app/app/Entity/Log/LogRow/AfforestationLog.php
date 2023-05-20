<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class AfforestationLog extends LogRow
{
    private Island $island;

    public function __construct(Island $island)
    {
        $this->island = $island;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'こころなしか、'],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => 'の森が増えたようです。'],
        ]);
    }
}
