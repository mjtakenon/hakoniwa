<?php

namespace App\Services\Hakoniwa\Plan;

use App\Services\Hakoniwa\Util\Point;

abstract class Plan
{
    public const PLAN_CASH_FLOW = 'cash_flow';

    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'data' => [
                'name' => $this->getName()
            ]
        ];
    }

    static public function fromJson(string $class, Plan|\stdClass $data): Plan
    {
        return new $class();
    }

    public function execute() {}

    public function getName():string { return ''; }
}
