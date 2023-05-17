<?php

namespace App\Entity\Log;

use App\Entity\Cell\Monster\Monster;
use App\Entity\Plan\Plan;
use App\Models\Island;

class MissileHitToMonsterLog extends LogRow
{
    private Island $island;
    private Monster $monster;
    private Plan $plan;

    public function __construct(Island $island, Monster $monster, Plan $plan)
    {
        $this->island = $island;
        $this->monster = $monster;
        $this->plan = $plan;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD],
            ['text' => 'から発射された'],
            ['text' => str_replace('発射', '', $this->plan->getName()), 'style' => StyleConst::BOLD],
            ['text' => 'は'],
            ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') ', 'style' => StyleConst::BOLD],
            ['text' => 'の'],
            ['text' => $this->monster->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'に命中、', 'style' => StyleConst::BOLD],
            ['text' => $this->monster->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            $this->monster->getHitPoints() >= 1 ? ['text' => 'は苦しそうに咆哮しました。'] : ['text' => 'は力尽き、倒れました。'],
        ]);
    }
}
