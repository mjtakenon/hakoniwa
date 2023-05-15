<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Util\Point;

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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') に'],
            ['text' => '巨大隕石', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'が落下！'],
        ]);
    }
}
