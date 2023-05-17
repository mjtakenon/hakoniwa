<?php

namespace App\Entity\Log;

use App\Models\Island;

class OccurRiotLog extends LogRow
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
            ['text' => 'にて'],
            ['text' => '住民による暴動', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'が発生！'],
        ]);
    }
}
