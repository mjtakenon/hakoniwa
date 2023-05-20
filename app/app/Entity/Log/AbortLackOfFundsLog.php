<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
use App\Models\Island;

class AbortLackOfFundsLog extends LogRow
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            is_null($this->plan::USE_POINT) ? ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') '] : ['text' => ''],
            ['text' => 'にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、'],
            ['text' => '資金不足', 'style' => StyleConst::BOLD],
            ['text' => 'により',],
            ['text' => '中止', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
