<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class AbortLackOfResourcesLog extends LogRow
{
    private Island $island;
    private ?Point $point;
    private Plan $plan;

    public function __construct(Island $island, ?Point $point, Plan $plan)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            is_null($this->point) ? ['text' => ''] : ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') '],
            ['text' => 'にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、'],
            ['text' => '資源不足', 'style' => StyleConst::BOLD],
            ['text' => 'により',],
            ['text' => '中止', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
