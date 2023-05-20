<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class AbortNoMissileBaseLog extends LogRow
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
            is_null($this->plan::USE_POINT) ? ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') '] : ['text' => ''],
            ['text' => $this->plan->getName(), 'style' => LogConst::BOLD],
            ['text' => 'は、利用可能な'],
            ['text' => 'ミサイル発射施設', 'style' => LogConst::BOLD],
            ['text' => 'が不足していたため'],
            ['text' => '中止', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
