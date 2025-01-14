<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Cell\Cell;
use App\Entity\Cell\CellConst;
use App\Entity\Cell\Monster\Monster;
use App\Entity\Cell\Others\Plain;
use App\Entity\Cell\Others\Wasteland;
use App\Entity\Log\LogConst;
use App\Entity\Log\LogRow;
use App\Models\Island;

class DestructionByEggLog extends LogRow
{
    private Island $island;
    private Monster $monster;
    private Cell $cell;

    public function __construct(Island $island, Monster $monster, Cell $cell)
    {
        $this->island = $island;
        $this->monster = $monster;
        $this->cell = $cell;
    }

    public function generate(): string
    {
        if ($this->cell::ATTRIBUTE[CellConst::IS_SHIP]) {
            return json_encode([
                ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
                ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
                ['text' => $this->monster::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                ['text' => 'が飛ばした謎の卵が'],
                ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') 地点'],
                ['text' => 'に落下、', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                ['text' => $this->cell::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_WARNING],
                ['text' => 'は海の藻屑になりました。']
            ]);
        }

        if ($this->cell::ATTRIBUTE[CellConst::IS_MONSTER]) {
            return json_encode([
                ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
                ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
                ['text' => $this->monster::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                ['text' => 'が飛ばした謎の卵が'],
                ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') 地点'],
                ['text' => 'に落下、', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                ['text' => $this->cell::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                ['text' => 'が捕食し'],
                ['text' => '体力を回復', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                ['text' => 'しました'],
            ]);
        }

        if ($this->cell::ELEVATION >= CellConst::ELEVATION_LAND) {
            if ($this->cell::TYPE !== Wasteland::TYPE) {
                return json_encode([
                    ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
                    ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
                    ['text' => $this->monster::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                    ['text' => 'が飛ばした謎の卵が'],
                    ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') 地点'],
                    ['text' => 'に落下、', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                    ['text' => $this->cell::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                    ['text' => 'は'],
                    ['text' => '衝撃で壊滅', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
                    ['text' => 'しました'],
                ]);
            }
        }

        return json_encode([
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => LogConst::BOLD],
            ['text' => ' (' . $this->monster->getPoint()->x . ',' . $this->monster->getPoint()->y . ') の'],
            ['text' => $this->monster::NAME, 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'が飛ばした謎の卵が'],
            ['text' => ' (' . $this->cell->getPoint()->x . ',' . $this->cell->getPoint()->y . ') 地点'],
            ['text' => 'に落下しました。']
        ]);
    }
}
