<?php

namespace App\Entity\Log;

use App\Entity\Cell\Cell;
use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
use App\Models\Island;

class AbortInvalidCellLog extends LogRow
{
    private Island $island;
    private Point $point;
    private Plan $plan;
    private Cell $cell;

    public function __construct(Island $island, Point $point, Plan $plan, Cell $cell)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
        $this->cell = $cell;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、予定地が'],
            ['text' => $this->cell::NAME, 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING],
            ['text' => 'だったため'],
            ['text' => '中止', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}