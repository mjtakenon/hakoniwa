<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Services\Hakoniwa\Cell\Monster\Monster;

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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
            ['text' => $this->monster::NAME, 'style' => StyleConst::BOLD.StyleConst::COLOR_DANGER ],
            ['text' => 'は、'],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            ['text' => 'に興味を失い帰っていきました...'],
        ]);
    }
}
