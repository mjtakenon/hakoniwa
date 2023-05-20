<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Monster\Monster;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;
use App\Models\Island;

class DisappearMonsterLog extends LogRow
{
    private Island $island;
    private Monster $monster;

    public function __construct(Island $island, Monster $monster)
    {
        $this->island = $island;
        $this->monster = $monster;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD ],
            ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
            ['text' => $this->monster::NAME, 'style' => LogConst::BOLD.LogConst::COLOR_DANGER ],
            ['text' => 'は、'],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD ],
            ['text' => 'に興味を失い帰っていきました...'],
        ]);
    }
}
