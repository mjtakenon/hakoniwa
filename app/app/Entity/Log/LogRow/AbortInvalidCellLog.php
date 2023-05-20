<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class AbortInvalidCellLog extends LogRow
{
    private Island $island;
    private Plan $plan;
    private Cell $cell;

    public function __construct(Island $island, Plan $plan, Cell $cell)
    {
        $this->island = $island;
        $this->plan = $plan;
        $this->cell = $cell;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => LogConst::BOLD],
            ['text' => 'は、予定地が'],
            ['text' => $this->cell::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_WARNING],
            ['text' => 'だったため'],
            ['text' => '中止', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
