<?php

namespace App\Services\Hakoniwa\Log;

use App\Services\Hakoniwa\Cell\Ship\CombatantShip;

class AbortReturnNotFoundLog extends LogRow
{
    private string $visibility;
    private CombatantShip $cell;

    public function __construct(CombatantShip $cell, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->visibility = $visibility;
        $this->cell = $cell;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => ''],
            ['text' => $this->cell->getAffiliationName() . '島'],
            ['text' => '所属の'],
            ['text' => $this->cell->getName(), 'style' => StyleConst::BOLD],
            ['text' => 'は、任務を終え、帰っていきました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
