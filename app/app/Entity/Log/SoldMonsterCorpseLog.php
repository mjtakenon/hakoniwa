<?php

namespace App\Entity\Log;

use App\Entity\Cell\Monster\Monster;

class SoldMonsterCorpseLog extends LogRow
{
    private Monster $monster;

    public function __construct(Monster $monster)
    {
        $this->monster = $monster;
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
}
