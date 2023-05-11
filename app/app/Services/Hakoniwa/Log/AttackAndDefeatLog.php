<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Cell\Cell;
use App\Services\Hakoniwa\Cell\Ship\CombatantShip;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class AttackAndDefeatLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private CombatantShip $offenciveShip;
    private CombatantShip $defenciveShip;
    private int $damage;

    public function __construct(Island $island, Turn $turn, CombatantShip $offenciveShip, CombatantShip $defenciveShip, int $damage)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->offenciveShip = $offenciveShip;
        $this->defenciveShip = $defenciveShip;
        $this->damage = $damage;
    }

    public static function create(Island $island, Turn $turn, CombatantShip $offenciveShip, CombatantShip $defenciveShip, int $damage)
    {
        return new static($island, $turn, $offenciveShip, $defenciveShip, $damage);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => ' (' . $this->offenciveShip->getPoint()->x . ',' . $this->offenciveShip->getPoint()->y . ') 地点の'],
            $this->offenciveShip->getAffiliationId() >= 1 ? ['text' => $this->offenciveShip->getAffiliationName() . '島所属', 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ] : ['text' => ''],
            ['text' => $this->offenciveShip->getName(), 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'が'],
            ['text' => ' (' . $this->defenciveShip->getPoint()->x . ',' . $this->defenciveShip->getPoint()->y . ') 地点の'],
            $this->defenciveShip->getAffiliationId() >= 1 ? ['text' => $this->defenciveShip->getAffiliationName() . '島所属', 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ] : ['text' => ''],
            ['text' => $this->defenciveShip->getName(), 'style' => StyleConst::BOLD.StyleConst::COLOR_WARNING ],
            ['text' => 'を攻撃!'],
            ['text' => '(破損率: ' . $this->defenciveShip->getDamage() - $this->damage . '%→' . $this->defenciveShip->getDamage() . '%)', 'style' => StyleConst::BOLD ],
            ['text' => '撃沈', 'style' => StyleConst::BOLD ],
            ['text' => 'しました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogVisibility::VISIBILITY_GLOBAL;
    }
}
