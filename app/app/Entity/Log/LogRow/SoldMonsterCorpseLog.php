<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Monster\Monster;
use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;

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
            ['text' => $this->monster->getName(), 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'の残骸は、'],
            ['text' => $this->monster->getCorpsePrice() . '億円', 'style' => LogConst::BOLD],
            ['text' => 'で売却されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return LogConst::VISIBILITY_PUBLIC;
    }
}
