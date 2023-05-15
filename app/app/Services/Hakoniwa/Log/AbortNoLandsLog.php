<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class AbortNoLandsLog extends LogRow
{
    private Island $island;
    private Point $point;
    private Plan $plan;

    public function __construct(Island $island, Point $point, Plan $plan)
    {
        $this->island = $island;
        $this->point = $point;
        $this->plan = $plan;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') にて予定されていた'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、予定地の周囲に陸地がなかったため'],
            ['text' => '中止', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'されました。'],
        ]);
    }
}
