<?php

namespace App\Entity\Log;

use App\Entity\Plan\Plan;
use App\Models\Island;

class ExecuteLog extends LogRow
{
    private Island $island;
    private Plan $plan;
    private string $visibility;

    public function __construct(Island $island, Plan $plan, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->plan = $plan;
        $this->visibility = $visibility;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => '' ],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'にて'],
            ['text' => $this->plan->getName(), 'style' => StyleConst::BOLD ],
            ['text' => 'が行われました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
