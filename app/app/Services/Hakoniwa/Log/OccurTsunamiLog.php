<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;

class OccurTsunamiLog extends LogRow
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
            ['text' => '津波', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'が発生！'],
        ]);
    }
}
