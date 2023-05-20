<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class MissileFiringLog extends LogRow
{
    private Island $island;
    private Plan $plan;
    private int $count;

    public function __construct(Island $island, Plan $plan, int $count)
    {
        $this->island = $island;
        $this->plan = $plan;
        $this->count = $count;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') に向けて'],
            ['text' => $this->count, 'style' => LogConst::BOLD],
            ['text' => '発の'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => LogConst::BOLD],
            ['text' => 'が発射されました。'],
        ]);
    }
}
