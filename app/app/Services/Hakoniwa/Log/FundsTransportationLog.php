<?php

namespace App\Services\Hakoniwa\Log;

use App\Models\Island;

class FundsTransportationLog extends LogRow
{
    private Island $island;
    private string $visibility;
    private int $amount;
    private bool $isFrom;

    public function __construct(Island $island, int $amount, bool $isFrom, string $visibility = LogVisibility::VISIBILITY_GLOBAL)
    {
        $this->island = $island;
        $this->visibility = $visibility;
        $this->amount = $amount;
        $this->isFrom = $isFrom;
    }

    public function generate(): string
    {
        return json_encode([
            $this->visibility === LogVisibility::VISIBILITY_PRIVATE ? ['text' => '(極秘) '] : ['text' => '' ],
            ['text' => $this->island->name . '島', 'link' => '/islands/' . $this->island->id, 'style' => StyleConst::BOLD ],
            $this->isFrom ? ['text' => 'へ'] : ['text' => 'から'],
            ['text' => $this->amount, 'style' => StyleConst::BOLD ],
            ['text' => '億円の'],
            ['text' => '送金', 'style' => StyleConst::BOLD ],
            ['text' => 'が実施されました。'],
        ]);
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}
