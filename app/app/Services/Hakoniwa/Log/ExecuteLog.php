<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class ExecuteLog implements ILog
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

    public function get(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->id . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => 'font-weight: bold;' ],
            ['text' => '(' . $this->point->x . ',' . $this->point->y . ') にて' . $this->plan->getName() . 'が行われました。'],
            ['text' => $this->plan->getName(), 'style' => 'font-weight: bold;' ],
            ['text' => 'が行われました。'],
        ]);
    }
}
