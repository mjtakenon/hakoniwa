<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Plan\Plan;

class SoldMonsterCorpseLog implements ILog
{
    private Turn $turn;
    private Monster $monster;

    public function __construct(Turn $turn, Monster $monster)
    {
        $this->turn = $turn;
        $this->monster = $monster;
    }

    public static function create(Turn $turn, Monster $monster)
    {
        return new static($turn, $monster);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->monster->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'の残骸は、'],
            ['text' => $this->monster->getCorpsePrice() . '億円', 'style' => StyleConst::BOLD],
            ['text' => 'で売却されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
