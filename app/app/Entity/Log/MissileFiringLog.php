<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Entity\Util\Point;
use App\Models\Island;

class MissileFiringLog extends LogRow
{
    private Island $island;
    private Point $point;
    private Plan $plan;
    private string $visibility;
    private int $count;

    public function __construct(Island $island, Point $point, Plan $plan, int $count, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
        $this->visibility = $visibility;
        $this->count = $count;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') に向けて'],
            ['text' => $this->count, 'style' => StyleConst::BOLD],
            ['text' => '発の'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD],
            ['text' => 'が発射されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
