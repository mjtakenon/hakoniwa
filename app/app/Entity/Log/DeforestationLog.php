<?php

namespace App\Entity\Log;

class DeforestationLog extends LogRow
{
    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => '伐採した木材から、'],
            ['text' => $this->amount, 'style' => StyleConst::BOLD],
            ['text' => '㌧の資源を得ました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_PUBLIC;
    }
}
