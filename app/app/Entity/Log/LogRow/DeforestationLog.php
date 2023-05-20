<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;

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
            ['text' => $this->amount, 'style' => LogConst::BOLD],
            ['text' => '㌧の資源を得ました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogConst::VISIBILITY_PUBLIC;
    }
}
