<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Util\Point;

class OccurVolcanicEruptionLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Point $point;

    public function __construct(Island $island, Turn $turn, Point $point)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->point = $point;
    }

    public static function create(Island $island, Turn $turn, Point $point)
    {
        return new static($island, $turn, $point);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') にて'],
            ['text' => '火山が噴火', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => '！'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
