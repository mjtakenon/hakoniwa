<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class ExecuteCellLog extends LogRow
{
    private Island $island;
    private Point $point;
    private Plan $plan;
    private string $visibility;

    public function __construct(Island $island, Point $point, Plan $plan, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
        $this->visibility = $visibility;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') にて'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD ],
            ['text' => 'が行われました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
