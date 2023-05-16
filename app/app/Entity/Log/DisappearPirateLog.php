<?php

namespace App\Entity\Log;

use App\Entity\Cell\Ship\Ship;
use App\Models\Island;

class DisappearPirateLog extends LogRow
{
    private Island $island;
    private Ship $ship;

    public function __construct(Island $island, Ship $ship)
    {
        $this->island = $island;
        $this->ship = $ship;
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
}
