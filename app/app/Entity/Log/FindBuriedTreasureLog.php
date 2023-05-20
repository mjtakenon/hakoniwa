<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
use App\Models\Island;

class FindBuriedTreasureLog extends LogRow
{
    private Island $island;
    private Plan $plan;
    private int $amount;

    public function __construct(Island $island, Plan $plan, int $amount)
    {
        $this->island = $island;
        $this->plan = $plan;
        $this->amount = $amount;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') の'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'を実施中に'],
            ['text' => '埋蔵金が発見', 'style' => StyleConst::BOLD],
            ['text' => 'され、'],
            ['text' => $this->amount, 'style' => StyleConst::BOLD],
            ['text' => '億円の臨時収入を得ました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_PUBLIC;
    }
}
