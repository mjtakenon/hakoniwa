<?php

namespace App\Entity\Cell\FundsProduction;

class LargeFactory extends Factory
{
    public const TYPE = 'large_factory';
    public const NAME = '大工場';
    const PRODUCTION_CAPACITY = 40000;
    const SEASIDE_PRODUCTION_CAPACITY = 60000;

    protected string $type = self::TYPE;
    protected string $name = self::NAME;
    protected int $productionCapacity = self::PRODUCTION_CAPACITY;
    protected int $seasideProductionCapacity = self::SEASIDE_PRODUCTION_CAPACITY;
}
