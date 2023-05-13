<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class FindBuriedTreasureLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Point $point;
    private Plan $plan;
    private int $amount;
    private string $visibility;

    public function __construct(Island $island, Turn $turn, Point $point, Plan $plan, int $amount, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->point = $point;
        $this->plan = $plan;
        $this->amount = $amount;
        $this->visibility = $visibility;
    }

    public static function create(Island $island, Turn $turn, Point $point, Plan $plan, int $amount, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $point, $plan, $amount, $visibility);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->point->x . ',' . $this->point->y . ') の'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD ],
            ['text' => 'を実施中に'],
            ['text' => '埋蔵金が発見', 'style' => StyleConst::BOLD ],
            ['text' => 'され、' ],
            ['text' => $this->amount, 'style' => StyleConst::BOLD ],
            ['text' => '億円の臨時収入を得ました。' ],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
