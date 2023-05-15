<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Ship\Ship;
use App\Services\Hakoniwa\Plan\Plan;

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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、利用可能な'],
            ['text' => $this->ship->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'が不足していたため'],
            ['text' => '中止', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
