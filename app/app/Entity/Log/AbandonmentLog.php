<?php

namespace App\Entity\Log;

use App\Models\Island;

class AbandonmentLog extends LogRow
{
    private Island $island;

    public function __construct(Island $island)
    {
        $this->island = $island;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'は放棄され、無人島になりました...'],
        ]);
    }
}
