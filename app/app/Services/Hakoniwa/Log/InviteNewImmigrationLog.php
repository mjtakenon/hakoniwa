<?php

namespace App\Services\Hakoniwa\Log;

class InviteNewImmigrationLog extends LogRow
{
    public function generate(): string
    {
        return json_encode([
            ['text' => '新たな入植者を得ましたが、'],
            ['text' => '発展ポイントが激減', 'style' => StyleConst::BOLD . StyleConst::COLOR_DANGER],
            ['text' => 'しました...'],
        ]);
    }
}
