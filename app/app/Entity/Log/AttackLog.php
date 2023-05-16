<?php

namespace App\Entity\Log;

use App\Entity\Cell\Ship\CombatantShip;

class AttackLog extends LogRow
{
    private CombatantShip $offenciveShip;
    private CombatantShip $defenciveShip;
    private int $damage;

    public function __construct(CombatantShip $offenciveShip, CombatantShip $defenciveShip, int $damage)
    {
        $this->offenciveShip = $offenciveShip;
        $this->defenciveShip = $defenciveShip;
        $this->damage = $damage;
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => ' (' . $this->offenciveShip->getPoint()->x . ',' . $this->offenciveShip->getPoint()->y . ') 地点の'],
            $this->offenciveShip->getAffiliationId() >= 1 ? ['text' => $this->offenciveShip->getAffiliationName() . '島所属', 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING] : ['text' => ''],
            $this->offenciveShip->getAffiliationId() >= 1 ? ['text' => $this->offenciveShip->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING] : ['text' => $this->offenciveShip->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'が'],
            ['text' => ' (' . $this->defenciveShip->getPoint()->x . ',' . $this->defenciveShip->getPoint()->y . ') 地点の'],
            $this->defenciveShip->getAffiliationId() >= 1 ? ['text' => $this->defenciveShip->getAffiliationName() . '島所属', 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING] : ['text' => ''],
            $this->defenciveShip->getAffiliationId() >= 1 ? ['text' => $this->defenciveShip->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_WARNING] : ['text' => $this->defenciveShip->getName(), 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'を攻撃!'],
            ['text' => '(破損率: ' . $this->defenciveShip->getDamage() - $this->damage . '%→' . $this->defenciveShip->getDamage() . '%)', 'style' => StyleConst::BOLD],
        ]);
    }
}
