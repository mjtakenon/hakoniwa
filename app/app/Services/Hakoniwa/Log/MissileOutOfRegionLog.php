<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class MissileOutOfRegionLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Point $point;
    private Plan $plan;

    public function __construct(Island $island, Turn $turn, Point $point, Plan $plan)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->point = $point;
        $this->plan = $plan;
    }

    public static function create(Island $island, Turn $turn, Point $point, Plan $plan)
    {
        return new static($island, $turn, $point, $plan);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'から発射された'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD ],
            ['text' => 'は'],
            ['text' => '領域外の海 (' . $this->point->x . ',' . $this->point->y . ') ', 'style' => StyleConst::BOLD],
            ['text' => 'に落下しました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
