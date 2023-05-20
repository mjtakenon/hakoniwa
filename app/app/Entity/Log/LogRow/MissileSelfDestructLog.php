<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
use App\Models\Island;

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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => 'から発射された'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => LogConst::BOLD],
            ['text' => 'は'],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') 地点上空で自爆', 'style' => LogConst::BOLD],
            ['text' => 'しました。'],
        ]);
    }
}
