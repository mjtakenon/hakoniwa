<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;

class OccurRiotLog implements ILog
{
    private Island $island;
    private Turn $turn;

    public function __construct(Island $island, Turn $turn)
    {
        $this->island = $island;
        $this->turn = $turn;
    }

    public static function create(Island $island, Turn $turn)
    {
        return new static($island, $turn);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'にて'],
            ['text' => '住民による暴動' , 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER],
            ['text' => 'が発生！'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
