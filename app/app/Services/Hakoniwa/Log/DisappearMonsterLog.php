<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Monster\Monster;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class DisappearMonsterLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Monster $monster;

    public function __construct(Island $island, Turn $turn, Monster $monster)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->monster = $monster;
    }

    public static function create(Island $island, Turn $turn, Monster $monster)
    {
        return new static($island, $turn, $monster);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
            ['text' => $this->monster::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
            ['text' => 'は、'],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'に興味を失い帰っていきました...'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
