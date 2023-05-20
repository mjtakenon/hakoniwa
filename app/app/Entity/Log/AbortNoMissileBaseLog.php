<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            is_null($this->plan::USE_POINT) ? ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') '] : ['text' => ''],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、利用可能な'],
            ['text' => 'ミサイル発射施設', 'style' => StyleConst::BOLD],
            ['text' => 'が不足していたため'],
            ['text' => '中止', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
