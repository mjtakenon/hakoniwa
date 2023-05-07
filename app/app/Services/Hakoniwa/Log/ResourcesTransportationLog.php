<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;
use App\Models\Turn;
use App\Services\Hakoniwa\Plan\FoodsTransportationPlan;
use App\Services\Hakoniwa\Plan\Plan;
use App\Services\Hakoniwa\Util\Point;

class ResourcesTransportationLog implements ILog
{
    private Island $island;
    private Turn $turn;
    private string $visibility;
    private int $amount;
    private bool $isFrom;

    public function __construct(Island $island, Turn $turn, int $amount, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->turn = $turn;
        $this->visibility = $visibility;
        $this->amount = $amount;
        $this->isFrom = $isFrom;
    }

    public static function create(Island $island, Turn $turn, int $amount, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        return new static($island, $turn, $amount, $isFrom, $visibility);
    }

    public function generate(): string
    {
        return json_encode([
            ['text' => 'ターン ' . $this->turn->turn . ' : '],
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => '' ],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            $this->isFrom ? ['text' => 'へ'] : ['text' => 'から'],
            ['text' => $this->amount, 'style' => StyleConst::BOLD ],
            ['text' => '㌧の'],
            ['text' => '資源輸送', 'style' => StyleConst::BOLD ],
            ['text' => 'が実施されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
