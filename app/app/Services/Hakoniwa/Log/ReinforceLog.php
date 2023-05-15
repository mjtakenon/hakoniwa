<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\Plan;

class ReinforceLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private string $visibility;
    private int $amount;
    private Plan $plan;
    private bool $isFrom;

    public function __construct(Island $island, Turn $turn, int $amount, Plan $plan, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->visibility = $visibility;
        $this->amount = $amount;
        $this->plan = $plan;
        $this->isFrom = $isFrom;
    }

    public static function create(Island $island, Turn $turn, int $amount, Plan $plan, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $amount, $plan, $isFrom, $visibility);
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            $this->isFrom ? ['text' => 'へ'] : ['text' => 'から'],
            ['text' => $this->amount, 'style' => StyleConst::BOLD],
            ['text' => '隻の'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'が実施されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
