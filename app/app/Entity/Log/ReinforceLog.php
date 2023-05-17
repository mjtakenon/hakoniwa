<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Models\Island;

class ReinforceLog extends LogRow
{
    private Island $island;
    private string $visibility;
    private int $amount;
    private Plan $plan;
    private bool $isFrom;

    public function __construct(Island $island, int $amount, Plan $plan, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->visibility = $visibility;
        $this->amount = $amount;
        $this->plan = $plan;
        $this->isFrom = $isFrom;
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