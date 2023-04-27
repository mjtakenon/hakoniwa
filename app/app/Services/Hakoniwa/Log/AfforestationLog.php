<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class AfforestationLog implements ILog
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
            ['text' => 'こころなしか、'],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'の森が増えたようです。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}