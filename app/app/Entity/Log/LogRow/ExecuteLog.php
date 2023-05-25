<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Entity\Plan\Plan;
use App\Models\Island;

class ExecuteLog extends LogRow
{
    private Island $island;
    private Plan $plan;
    private string $visibility;

    public function __construct(Island $island, Plan $plan, string $visibility = LogConst::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->plan = $plan;
        $this->visibility = $visibility;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogConst::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD ],
            $this->plan::USE_POINT ? ['text' => ' (' . $this->plan->getPoint()->x . ',' . $this->plan->getPoint()->y . ') にて'] : ['text' => 'にて'],
            ['text' => $this->plan->getName(), 'style' => LogConst::BOLD ],
            ['text' => 'が行われました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
