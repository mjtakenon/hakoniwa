<?php

namespace App\Entity\Log;

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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'の森が増えたようです。'],
        ]);
    }
}
