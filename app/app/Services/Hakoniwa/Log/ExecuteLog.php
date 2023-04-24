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
    private Plan $plan;

    public function __construct(Island $island, Turn $turn, Plan $plan)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->plan = $plan;
    }

    public static function create(Island $island, Turn $turn, Plan $plan)
    {
        return new static($island, $turn, $plan);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'にて'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD ],
            ['text' => 'が行われました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
