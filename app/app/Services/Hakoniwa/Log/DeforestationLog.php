<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class DeforestationLog implements ILog
{
    private Turn $turn;
    private int $amount;

    public function __construct(Turn $turn, int $amount)
    {
        $this->turn = $turn;
        $this->amount = $amount;
    }

    public static function create(Turn $turn, int $amount)
    {
        return new static($turn, $amount);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => '伐採した木材から、'],
            ['text' => $this->amount , 'style' => StyleConst::BOLD],
            ['text' => '㌧の資源を得ました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_PUBLIC;
    }
}
