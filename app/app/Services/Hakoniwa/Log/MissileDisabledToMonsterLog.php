<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Plan\Plan;

class MissileDisabledToMonsterLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Cell $cell;
    private Plan $plan;

    public function __construct(Island $island, Turn $turn, Cell $cell, Plan $plan)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->cell = $cell;
        $this->plan = $plan;
    }

    public static function create(Island $island, Turn $turn, Cell $cell, Plan $plan)
    {
        return new static($island, $turn, $cell, $plan);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'から発射された'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD],
            ['text' => 'は'],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') ', 'style' => StyleConst::BOLD],
            ['text' => 'の'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'に命中しましたが、'],
            ['text' => '無効化', 'style' => StyleConst::BOLD],
            ['text' => 'されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
