<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Status\Status;

class SummaryLog implements ILog
{
    private Status $status;
    private Status $prevStatus;
    private Turn $turn;

    public function __construct(Status $status, Status $prevStatus, Turn $turn)
    {
        $this->status = $status;
        $this->prevStatus = $prevStatus;
        $this->turn = $turn;
    }

    public static function create(Status $status, Status $prevStatus, Turn $turn)
    {
        return new static($status, $prevStatus, $turn);
    }

    public function get(): string
    {
        $foods = $this->status->getFoods() - $this->prevStatus->getFoods();
        $funds = $this->status->getFunds() - $this->prevStatus->getFunds();
        $population = $this->status->getPopulation() - $this->prevStatus->getPopulation();
        $developmentPoints = $this->status->getDevelopmentPoints() - $this->prevStatus->getDevelopmentPoints();

        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            ['text' => '収支', 'style' => StyleConst::BOLD ],
            ['text' => ' ： '],
            ['text' => '食料：', 'style' => StyleConst::BOLD],
            $foods < 0 ? ['text' => '' . $foods . '㌧', 'style' => StyleConst::COLOR_DANGER] : ($foods === 0 ? ['text' => '' . $foods . '㌧'] : ['text' => '+' . $foods . '㌧', 'style' => StyleConst::COLOR_LINK]),
            ['text' => '　'],
            ['text' => '資金：', 'style' => StyleConst::BOLD],
            $funds < 0 ? ['text' => '' . $funds . '億円', 'style' => StyleConst::COLOR_DANGER] : ($funds === 0 ? ['text' => '' . $funds . '億円'] : ['text' => '+' . $funds . '億円', 'style' => StyleConst::COLOR_LINK]),
            ['text' => '　'],
            ['text' => '人口：', 'style' => StyleConst::BOLD],
            $population < 0 ? ['text' => '' . $population . '人', 'style' => StyleConst::COLOR_DANGER] : ($population === 0 ? ['text' => '' . $population . '人']:['text' => '+' . $population . '人', 'style' => StyleConst::COLOR_LINK]),
            ['text' => '　'],
            ['text' => 'ポイント：', 'style' => StyleConst::BOLD],
            $developmentPoints < 0 ? ['text' => '' . $developmentPoints . 'pts', 'style' => StyleConst::COLOR_DANGER] : ($developmentPoints === 0 ? ['text' => '' . $developmentPoints . 'pts']:['text' => '+' . $developmentPoints . 'pts', 'style' => StyleConst::COLOR_LINK]),
        ]);
    }
}
