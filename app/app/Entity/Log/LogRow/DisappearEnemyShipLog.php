<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Ship\Ship;
use App\Entity\Log\LogConst;
use App\Entity\Log\LogRow;
use App\Models\Island;

class DisappearEnemyShipLog extends LogRow
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
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->ship->getPoint()->x . ',' . $this->ship->getPoint()->y . ') の'],
            ['text' => $this->ship::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'は、'],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => 'に興味を失い帰っていきました...'],
        ]);
    }
}
