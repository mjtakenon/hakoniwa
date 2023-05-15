<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Ship\Ship;

class DisappearPirateLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private Ship $ship;

    public function __construct(Island $island, Turn $turn, Ship $ship)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->ship = $ship;
    }

    public static function create(Island $island, Turn $turn, Ship $ship)
    {
        return new static($island, $turn, $ship);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->ship->getPoint()->x . ',' . $this->ship->getPoint()->y . ') の'],
            ['text' => $this->ship::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
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
