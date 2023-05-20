<?php

namespace App\Entity\Log;

use App\Entity\Cell\Ship\CombatantShip;

class AbortReturnNotFoundLog extends LogRow
{
    private CombatantShip $cell;

    public function __construct(CombatantShip $cell)
    {
        $this->cell = $cell;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => $this->cell->getAffiliationName() . '島'],
            ['text' => '所属の'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、任務を終え、帰っていきました。'],
        ]);
    }
}
