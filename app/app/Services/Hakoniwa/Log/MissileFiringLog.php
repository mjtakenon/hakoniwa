<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class MissileFiringLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Point $point;
    private Plan $plan;
    private string $visibility;
    private int $count;

    public function __construct(Island $island, Turn $turn, Point $point, Plan $plan, int $count, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->point = $point;
        $this->plan = $plan;
        $this->visibility = $visibility;
        $this->count = $count;
    }

    public static function create(Island $island, Turn $turn, Point $point, Plan $plan, int $count, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $point, $plan, $count, $visibility);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => '' ],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') に向けて'],
            ['text' => $this->count, 'style' => StyleConst::BOLD ],
            ['text' => '発の'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD ],
            ['text' => 'が発射されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}