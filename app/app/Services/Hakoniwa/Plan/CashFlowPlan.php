<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Plan\Plan;

class CashFlowPlan extends Plan
{
    public const NAME = '資金繰り';

    public function getName():string { return self::NAME; }

}
