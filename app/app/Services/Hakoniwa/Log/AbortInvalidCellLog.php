<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class AbortInvalidCellLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Point $point;
    private Plan $plan;
    private Cell $cell;

    public function __construct(Island $island, Turn $turn, Point $point, Plan $plan, Cell $cell)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->point = $point;
        $this->plan = $plan;
        $this->cell = $cell;
    }

    public static function create(Island $island, Turn $turn, Point $point, Plan $plan, Cell $cell)
    {
        return new static($island, $turn, $point, $plan, $cell);
    }

    public function get(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->id . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => '(' . $this->point->x . ',' . $this->point->y . ') にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD.StyleConst::COLOR_PRIMARY ],
            ['text' => 'は、予定地が'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'だったため'],
            ['text' => '中止', 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
            ['text' => 'されました。'],
        ]);
    }
}
