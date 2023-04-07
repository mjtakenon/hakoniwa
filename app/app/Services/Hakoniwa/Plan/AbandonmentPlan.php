<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

class AbandonmentPlan extends Plan
{
    public const KEY = 'abandonment';

    public const NAME = '島の放棄';
    public const PRICE = 0;

    public function __construct()
    {
        parent::__construct();
        $this->key = self::KEY;
        $this->name = self::NAME;
        $this->price = self::PRICE;
    }
    public function getPrice(): string
    {
        return '(無料)';
    }

    public function execute(Point $point, int $amount): void
    {
        // TODO: Implement execute() method.
    }
}
