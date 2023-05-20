<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class AbortInvalidTerrainLog extends LogRow
{
    private Island $island;
    private Plan $plan;

    public function __construct(Island $island, Plan $plan)
    {
        $this->island = $island;
        $this->plan = $plan;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => 'にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => LogConst::BOLD],
            ['text' => 'は、'],
            ['text' => '条件に合う地形が存在しなかった', 'style' => LogConst::BOLD],
            ['text' => 'ため',],
            ['text' => '中止', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
