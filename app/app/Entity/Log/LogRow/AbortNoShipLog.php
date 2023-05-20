<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Ship\Ship;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class AbortNoShipLog extends LogRow
{
    private Island $island;
    private Plan $plan;
    private Ship $ship;

    public function __construct(Island $island, Plan $plan, Ship $ship)
    {
        $this->island = $island;
        $this->plan = $plan;
        $this->ship = $ship;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => 'にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => LogConst::BOLD],
            ['text' => 'は、利用可能な'],
            ['text' => $this->ship->getName(), 'style' => LogConst::BOLD],
            ['text' => 'が不足していたため'],
            ['text' => '中止', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
