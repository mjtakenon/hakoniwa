<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Util\Point;
use App\Models\Island;

class OccurHugeMeteoriteLog extends LogRow
{
    private Island $island;
    private Point $point;

    public function __construct(Island $island, Point $point)
    {
        $this->island = $island;
        $this->point = $point;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') に'],
            ['text' => '巨大隕石', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'が落下！'],
        ]);
    }
}
