<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class MissileSelfDestructLog extends LogRow
{
    private Island $island;
    private Point $point;
    private Plan $plan;

    public function __construct(Island $island, Point $point, Plan $plan)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'から発射された'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD],
            ['text' => 'は'],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') 地点上空で自爆', 'style' => StyleConst::BOLD],
            ['text' => 'しました。'],
        ]);
    }
}
