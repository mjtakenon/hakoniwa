<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Log\LogConst;
use App\Entity\Log\LogRow;
use App\Models\Island;

class ReceiveBountyLog extends LogRow
{
    private Cell $cell;
    private int $amount;

    public function __construct(Cell $cell, int $amount)
    {
        $this->cell = $cell;
        $this->amount = $amount;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->cell->getName(), 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'にかけられていた懸賞金'],
            ['text' => $this->amount . '億円', 'style' => LogConst::BOLD],
            ['text' => 'を受け取りました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogConst::VISIBILITY_PUBLIC;
    }
}
