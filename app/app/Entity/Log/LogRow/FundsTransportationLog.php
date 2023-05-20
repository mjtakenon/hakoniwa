<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class FundsTransportationLog extends LogRow
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD ],
            $this->isFrom ? ['text' => 'へ'] : ['text' => 'から'],
            ['text' => $this->amount, 'style' => LogConst::BOLD ],
            ['text' => '億円の'],
            ['text' => '送金', 'style' => LogConst::BOLD ],
            ['text' => 'が実施されました。'],
        ]);
    }
}
