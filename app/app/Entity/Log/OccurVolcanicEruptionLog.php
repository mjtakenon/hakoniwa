<?php

namespace App\Entity\Log;

use App\Entity\Util\Point;
use App\Models\Island;

class OccurVolcanicEruptionLog extends LogRow
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') にて'],
            ['text' => '火山が噴火', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => '！'],
        ]);
    }
}