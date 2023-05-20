<?php

namespace App\Entity\Log\LogRow;

use App\Entity\Log\LogRow;
use App\Entity\Log\LogConst;

class InviteNewImmigrationLog extends LogRow
{
    public function generate(): string
    {
        return json_encode([
            ['text' => '新たな入植者を得ましたが、'],
            ['text' => '発展ポイントが激減', 'style' => LogConst::BOLD . LogConst::COLOR_DANGER],
            ['text' => 'しました...'],
        ]);
    }
}
