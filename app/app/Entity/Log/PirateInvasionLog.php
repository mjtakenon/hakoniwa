<?php

namespace App\Entity\Log;

use App\Models\Island;

class PirateInvasionLog extends LogRow
{
    private Island $island;

    public function __construct(Island $island)
    {
        $this->island = $island;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'に'],
            ['text' => '海賊', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'が出現！'],
        ]);
    }
}
