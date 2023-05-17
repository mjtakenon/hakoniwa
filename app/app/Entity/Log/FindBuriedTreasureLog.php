<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
use App\Models\Island;

class FindBuriedTreasureLog extends LogRow
{
    private Island $island;
    private Point $point;
    private Plan $plan;
    private int $amount;
    private string $visibility;

    public function __construct(Island $island, Point $point, Plan $plan, int $amount, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
        $this->amount = $amount;
        $this->visibility = $visibility;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') の'],
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
        return $this->visibility;
    }
}
