<?php

namespace App\Entity\Log;

use App\Models\Island;

class ResourcesTransportationLog extends LogRow
{
    private Island $island;
    private int $amount;
    private bool $isFrom;

    public function __construct(Island $island, int $amount, bool $isFrom)
    {
        $this->island = $island;
        $this->amount = $amount;
        $this->isFrom = $isFrom;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            $this->isFrom ? ['text' => 'へ'] : ['text' => 'から'],
            ['text' => $this->amount, 'style' => StyleConst::BOLD],
            ['text' => '㌧の'],
            ['text' => '資源輸送', 'style' => StyleConst::BOLD],
            ['text' => 'が実施されました。'],
        ]);
    }
}
