<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class ReinforceLog extends LogRow
{
    private Island $island;
    private int $amount;
    private Plan $plan;
    private bool $isFrom;

    public function __construct(Island $island, int $amount, Plan $plan, bool $isFrom)
    {
        $this->island = $island;
        $this->amount = $amount;
        $this->plan = $plan;
        $this->isFrom = $isFrom;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            $this->isFrom ? ['text' => 'へ'] : ['text' => 'から'],
            ['text' => $this->amount, 'style' => LogConst::BOLD],
            ['text' => '隻の'],
            ['text' => $this->plan->getName(), 'style' => LogConst::BOLD],
            ['text' => 'が実施されました。'],
        ]);
    }
}
