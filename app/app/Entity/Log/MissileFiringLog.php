<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') に向けて'],
            ['text' => $this->count, 'style' => StyleConst::BOLD],
            ['text' => '発の'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD],
            ['text' => 'が発射されました。'],
        ]);
    }
}
